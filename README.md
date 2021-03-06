# Swallowing Error Handler in Zend Expressive 1.0.3

This small project demonstrates the internals of PHP's error handling.

It is written for [Zend Expressive](https://github.com/zendframework/zend-expressive)
to demonstrate how the error handler in [Expressive 1.0.3](https://github.com/zendframework/zend-expressive/blob/1.0.3/src/Application.php#L142) swallows all errors.

## Demonstration

Install the composer packages:

	composer install --no-dev

Run the demonstration with PHP's integrated web server:

	php -S localhost:8080 -t public

## Observations

An [error handler](src/ErrorHandler.php) which converts errors to ErrorExceptions
is registered at the start of the script.
Additionally, it prints the registration message to `stderr` when called.

However, due to the registration of an error handler in Expressive, it is never called.
Since it returns `false`, PHP's internal error handler
prints the error on the console and execution resumes as normal.
[See Error Handling in PHP](#error-handling-in-php).

Modify some variables in [public/index.php](public/index.php) to investigate the behaviour.

The default is:

	Test\Globals::$registerHandlerInMiddleware = false;
	Test\Globals::$restoreBeforeRegistering = false;
	Test\Globals::$triggerError = true;

### Register Another Error Handler to Fix the Issue

	Test\Globals::$registerHandlerInMiddleware = true;

**A second error handler** can be registered in [ErrorMiddleware](src/ErrorMiddleware.php).
Therefore, errors are converted to ErrorExceptions and can be caught by the middleware.

Middleware should not be required to install a new handler,
because they can not know what the previous handler was.

### Removing Expressive's Error Handler

	Test\Globals::$restoreBeforeRegistering = true;

If Expressive's error handler is removed by popping it from the handler stack,
the global error handler can handle the error.

The handler should not simply be popped, otherwise pushing and popping is not consistent.

## Consequences

An error handler registered before running the application is not usable.

I believe, it is a bug in Expressive which should be fixed.

# Error Handling in PHP

At most two error handlers in PHP are active at the same time.
User defined error handlers using [`set_error_handler`](http://php.net/set-error-handler)
are implemented as a stack, but only the top handler is executed.

* If the error handler returns `false`, PHP's internal error handler is executed as well.
* If the error handler was registered with an error type mask, it is not executed
and PHP's internal error handler is executed immediately.

The implementation of this behaviour is in
[Zend/zend.c:1200](https://github.com/php/php-src/blob/PHP-5.6.28/Zend/zend.c#L1200)
and the following lines.

## PHP's internal error handler and the `@` operator

Only PHP's internal error handler sets the error reported by
[`error_get_last`](http://php.net/error-get-last).
This means: If a user defined error handler is registered,
the last error can only be retrieved if the handler returns false.

Error handlers are executed, if registered with the appropriate bitmask,
but regardless whether the statement was prepended with the `@` operator.
However, [`error_reporting`](http://php.net/error-reporting)
returns zero, if the `@` was prepended.
