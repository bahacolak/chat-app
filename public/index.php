<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new \DI\ContainerBuilder();
$container = $containerBuilder->build();
AppFactory::setContainer($container);

$app = AppFactory::create();

$routes = require __DIR__ . '/../app/Routes/api.php';
$routes($app, $container);

$app->run();
