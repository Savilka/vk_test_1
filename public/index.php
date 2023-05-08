<?php

use App\Controllers\EventController;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use RKA\Middleware\IpAddress;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$checkProxyHeaders = true;
$trustedProxies = ['10.0.0.1', '10.0.0.2'];
$app->add(new IpAddress($checkProxyHeaders, $trustedProxies));

$app->group('/api', function (RouteCollectorProxy $group) {
    $group->get('/addEvent', [EventController::class, 'addEvent']);

    $group->post('/getEvents', [EventController::class, 'getEvents']);
});

$app->addErrorMiddleware(false, false, false);

$app->run();