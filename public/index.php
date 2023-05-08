<?php

use App\Controllers\EventController;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->group('/api', function (RouteCollectorProxy $group) {
    $group->get('/addEvent', [EventController::class, 'addEvent']);

    $group->post('/getEvents', [EventController::class, 'getEvents']);
});

$app->run();