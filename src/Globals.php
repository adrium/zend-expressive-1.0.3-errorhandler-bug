<?php

namespace Test;

abstract class Globals
{
	static $stderr;
	static $registerHandlerInMiddleware;
	static $restoreBeforeRegistering;
	static $triggerError;
}

Globals::$stderr = fopen('php://stderr', 'a+');
Globals::$registerHandlerInMiddleware = true;
Globals::$restoreBeforeRegistering = false;
Globals::$triggerError = false;
