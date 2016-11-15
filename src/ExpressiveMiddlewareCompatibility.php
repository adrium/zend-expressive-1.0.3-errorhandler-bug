<?php

namespace Test;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Interop\Http\Middleware\DelegateInterface as Delegate;
use Interop\Http\Middleware\ServerMiddlewareInterface as Middleware;


trait ExpressiveMiddlewareCompatibility
{
	public function __invoke(Request $req, Response $res, $next)
	{
		return $this->process($req, $next);
	}
}
