<?php

use Nebalus\Webapi\Config\GeneralConfig;
use Nebalus\Webapi\Factory\DiContainerFactory;
use Nebalus\Webapi\Factory\LoggerFactory;
use Nebalus\Webapi\Slim\RouteCollector;
use Slim\App;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $diContainerFactory = new DiContainerFactory();
    $diContainer = $diContainerFactory->build();

    $slimApp = AppFactory::createFromContainer($diContainer);
    $diContainer->set(App::class, $slimApp);

    $routes = $diContainer->get(RouteCollector::class);
    $routes->init();

    $slimApp->run();
} catch (Throwable $e) {
    $loggerFactory = new LoggerFactory(new GeneralConfig());
    $logger = $loggerFactory();
    $logger->error($e->getMessage(), $e->getTrace());
}
