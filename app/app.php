<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Knp\Provider\ConsoleServiceProvider;
use Inquirer\Command;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/../debug.log',
));

$baseStoragePath = __DIR__ . '/../storage';

$app['baseStoragePath'] = $baseStoragePath;

$app['botStorage'] = function () use ($baseStoragePath) {
    return new \Inquirer\EntityStorage($baseStoragePath . DIRECTORY_SEPARATOR . 'bots.json');
};

$app['api'] = function () {
    return new \Inquirer\Api();
};

$app->before(function (Request $request) {
    if (strpos($request->headers->get('Content-Type'), 'application/json') === 0) {
        $data = json_decode($request->getContent(), true);

        $request->request->replace(is_array($data) ? $data : []);
    }
});

$app->get('/debug', function () {
    return file_get_contents('../debug.log');
});

$app->post('/bot/register', function(Request $request) use ($app) {
    $botService = new \Inquirer\Service\BotService($app['api']);
    $userName = $request->request->get('userName');
    $token = $request->request->get('token');

    if (!$userName || !$token) {
        return new Response('Username and token must be set.', 422);
    }

    try {
        $botService->register($request->get('userName'), $request->get('token'));

        return new Response('Bot registered.', 200);
    } catch (\Exception $exception) {
        return new Response($exception->getMessage(), 500);
    }
});

$app->match('/webhook/{botUsername}', function(Request $request, $botUsername) {
    $webhook = new \Inquirer\Webhook();
    $data = $request->getContent();
    \Inquirer\Registry::getInstance()->getLog()->debug("Incoming data: {$data}");
    $webhook->process($botUsername, json_decode($data));
    exit;
});

$app->register(new ConsoleServiceProvider());
$console = $app['console'];
$console->add(new Command\Bot\Register());
$console->add(new Command\Bot\GetList());

\Inquirer\Registry::getInstance()->setApp($app);

return $app;
