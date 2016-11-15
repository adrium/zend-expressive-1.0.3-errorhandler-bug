<?php

namespace Test;

use RuntimeException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Interop\Http\Middleware\DelegateInterface as Delegate;
use Interop\Http\Middleware\ServerMiddlewareInterface as Middleware;
use Zend\Diactoros\Response\HtmlResponse;

class TestMiddleware
implements Middleware
{
	use ExpressiveMiddlewareCompatibility;

	public function process(Request $request, Delegate $delegate)
	{
		if (Globals::$triggerError)
			trigger_error('Test Error');
		return new HtmlResponse('You see a test');
	}
}
