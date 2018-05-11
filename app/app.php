<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Knp\Provider\ConsoleServiceProvider;
use Inquirer\Command;
use Symfony\Component\HttpFoundation\Request;

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
