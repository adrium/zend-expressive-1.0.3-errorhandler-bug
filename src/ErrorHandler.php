<?php

namespace Test;

use ErrorException;

class ErrorHandler
{
	private $message;

	public function register($message)
	{
		$this->message = $message;
		set_error_handler([$this, 'handleError']);
	}

	public function unregister()
	{
		restore_error_handler();
	}

	public function handleError($no, $str, $file, $line)
	{
		if (error_reporting() === 0) return true;
		fwrite(Globals::$stderr, $this->message);
		throw new ErrorException($str, 0, $no, $file, $line);
	}
}
