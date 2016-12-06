<?php

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

Test\Globals::$registerHandlerInMiddleware = false;
Test\Globals::$restoreBeforeRegistering = false;
Test\Globals::$triggerError = true;

$container = require 'config/container.php';

$container->get('ErrorHandler 1')->register("\033[32mGlobal error handler\033[0m\n");

$app = $container->get(Zend\Expressive\Application::class);
$app->raiseThrowables();
$app->run();
