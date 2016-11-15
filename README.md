# PHP Error Handler Demonstration

This small project demonstrates the internals of PHP's error handling.

It is written for [Zend Expressive](https://github.com/zendframework/zend-expressive)
to demonstrate how the error handler in [Expressive 1.0.3](https://github.com/zendframework/zend-expressive/blob/1.0.3/src/Application.php#L142) swallows all errors.

## Error Handling in PHP

At most two error handlers in PHP are active at the same time.
User defined error handlers using [`set_error_handler`](http://php.net/set-error-handler)
are implemented as a stack, but only the top handler is executed.

* If the error handler returns `false`, PHP's internal error handler is executed as well.
* If the error handler was registered with an error type mask, it is not executed
and PHP's internal error handler is executed immediately.

For regular PHP scripts, PHP's internal error prints the error on `stderr`.

I believe the implementation of this behaviour is in
[Zend/zend.c](https://github.com/php/php-src/blob/PHP-5.6.28/Zend/zend.c#L1200).

## Demonstration

Install the composer packages:

	composer install --no-dev

Run the demonstration with PHP's integrated web server:

	php -S localhost:8080 -t public

## Observations

An [error handler](src/ErrorHandler.php) which converts errors to ErrorExceptions
is registered at the start of the script.
Additionally, it prints the registration message to `stderr`.

However, because of the described circumstances above, it is not executed as intended.

Use `Globals` in [public/index.php](public/index.php) to modify the behaviour.

The default is:

	Test\Globals::$registerHandlerInMiddleware = true;
	Test\Globals::$restoreBeforeRegistering = false;
	Test\Globals::$triggerError = true;

**A second error handler** is registered in [ErrorMiddleware](src/ErrorMiddleware.php).
Therefore, errors are converted to ErrorExceptions and can be caught by the middleware.

### No second error handler

	Test\Globals::$registerHandlerInMiddleware = false;
	Test\Globals::$restoreBeforeRegistering = false;

No second error handler is registered and it is assumed
that the global error handler (number one) is handles errors.

This is not the case, because Expressive registers
an error handler as described in the introduction.
Since it returns `false`, PHP's internal error handler
prints the error on the console and execution resumes as normal.

### Removing Expressive's error handler

	Test\Globals::$registerHandlerInMiddleware = false;
	Test\Globals::$restoreBeforeRegistering = true;

If Expressive's error handler is removed by popping it from the handler stack,
the global error handler can handle the error.

## Consequences

An error handler registered before running the application is not usable.

Middleware should not be required to install a new handler,
because they can not know what the previous handler was.

I believe, it is a bug in Expressive which should be fixed.
