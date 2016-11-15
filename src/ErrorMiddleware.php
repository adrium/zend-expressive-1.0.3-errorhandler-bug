<?php

namespace Test;

use Exception;
use Throwable;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Interop\Http\Middleware\DelegateInterface as Delegate;
use Interop\Http\Middleware\ServerMiddlewareInterface as Middleware;
use Zend\Diactoros\Response\HtmlResponse;

class ErrorMiddleware
implements Middleware
{
	use ExpressiveMiddlewareCompatibility;

	private $handler;

	public function __construct(ErrorHandler $handler)
	{
		$this->handler = $handler;
	}

	public function process(Request $request, Delegate $delegate)
	{
		if (Globals::$restoreBeforeRegistering)
			restore_error_handler();

		if (Globals::$registerHandlerInMiddleware)
			$this->handler->register("\033[33mError handler registered in Middleware\033[0m\n");

		try {
			$response = $delegate->process($request);
		} catch (Exception $e) {
			$response = $this->handleThrowable($e);
		} catch (Throwable $e) {
			$response = $this->handleThrowable($e);
		}

		if (Globals::$registerHandlerInMiddleware)
			$this->handler->unregister();

		$response->getBody()->seek(0, SEEK_END);
		$response->getBody()->write('<p>(Piped through ErrorMiddleware)</p>');

		return $response;
	}

	private function handleThrowable($e)
	{
		return new HtmlResponse(sprintf(
			'<h1>Exception</h1><p><strong>%s: %s</strong></p><p>In %s:%s</p>',
			get_class($e),
			$e->getMessage(),
			$e->getFile(),
			$e->getLine()
		));
	}
}
