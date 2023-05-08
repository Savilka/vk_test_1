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
$trustedProxies = null;
$headersToInspect = [
    'X-Real-IP',
    'Forwarded',
    'X-Forwarded-For',
    'X-Forwarded',
    'X-Cluster-Client-Ip',
    'Client-Ip',
];
$app->add(new IpAddress($checkProxyHeaders, $trustedProxies, 'ip_address', $headersToInspect));

$app->group('/api', function (RouteCollectorProxy $group) {
    $group->get('/addEvent', [EventController::class, 'addEvent']);

    $group->post('/getEvents', [EventController::class, 'getEvents']);
});

$app->addErrorMiddleware(false, false, false);

$app->run();