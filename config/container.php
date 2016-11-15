<?php

return _createContainer();

use Xtreamwayz\Pimple\Container as PimpleContainer;
use Zend\Expressive\Application;

function _createContainer()
{
	$container = new PimpleContainer;

	$container['config'] = new ArrayObject([
		'middleware_pipeline' => [
			['middleware' => Test\ErrorMiddleware::class],
			['middleware' => Test\TestMiddleware::class],
		],
	]);;

	$container[Application::class] =
		new Zend\Expressive\Container\ApplicationFactory;

	$container['ErrorHandler 1'] = function ($c) { return new Test\ErrorHandler; };
	$container['ErrorHandler 2'] = function ($c) { return new Test\ErrorHandler; };

	$container[Test\TestMiddleware::class] = function ($c) { return new Test\TestMiddleware; };
	$container[Test\ErrorMiddleware::class] = function ($c) {
		return new Test\ErrorMiddleware($c->get('ErrorHandler 2'));
	};

	return $container;
}
