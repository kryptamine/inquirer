<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Knp\Provider\ConsoleServiceProvider;
use Inquirer\Command;
use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();

$baseStoragePath = __DIR__ . '/../storage';

$app['botStorage'] = function () use ($baseStoragePath) {
    return new \Inquirer\EntityStorage($baseStoragePath . DIRECTORY_SEPARATOR . 'bots.json');
};

$app['api'] = function () {
    return new \Inquirer\Api();
};

$app->match('/webhook/{botUsername}', function(Request $request, $botUsername) {
    ob_start();
    var_dump($request->getContent());
    file_put_contents(__DIR__ . '/../request', ob_get_clean());
    return "Hello, {$botUsername}!";
});

$app->register(new ConsoleServiceProvider());
$console = $app['console'];
$console->add(new Command\Bot\Register());
$console->add(new Command\Bot\GetList());

return $app;
