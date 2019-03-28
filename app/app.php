<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();

// load dotenv
$dotEnv = Dotenv\Dotenv::create('../');
$dotEnv->load();

$app->register(new Silex\Provider\MonologServiceProvider(), [
    'monolog.logfile' => __DIR__ . '/../debug.log',
]);

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../views',
]);

$app['baseStoragePath'] = __DIR__ . '/../storage';

$app['api'] = function () {
    return new \Inquirer\Api();
};

$app->get('/debug', function () {
    return file_get_contents('../debug.log');
});

$app->get('/top', function (Request $request) use ($app) {
    $calculator = new \Inquirer\Services\StatisticCalculator(
        __DIR__.'/../storage/chats',
        (bool)$request->get('hide', false)
    );
    if ($filterBy = $request->get('filterBy')) {
        $calculator->filterBy($filterBy);
    }
    if ($request->get('json')) {
        return $app->json($calculator->collect());
    }
    return $app['twig']->render('top.html', ['participants' => $calculator->collect()]);

});


try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram(getenv('API_KEY'), getenv('BOT_USERNAME'));
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    \Inquirer\Registry::getInstance()->getLog()->error("Telegram error: {$e->getMessage()}");
}

$app->match('/register', function() use ($telegram) {
    $telegram->setWebhook(getenv('WEBHOOK_URL'));
    exit;
});

$app->match('/webhook', function(Request $request) {
    $data = $request->getContent();
    \Inquirer\Registry::getInstance()->getLog()->debug("Incoming data: {$data}");

    try {
        (new \Inquirer\Webhook())->process(json_decode($data));
    } catch (\Exception $exception) {
        \Inquirer\Registry::getInstance()->getLog()->debug("WebHook error: {$exception->getMessage()}");
    }
    exit;
});

\Inquirer\Registry::getInstance()->setApp($app);

return $app;
