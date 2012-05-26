<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/ErrorHandler.php
 * error handler
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
$sb->provide("core/lib/ErrorHandler");
/**
 * Error handler
 * @ingroup core
 */
class ErrorHandler {

	function handle_exception($exception) {
		restore_error_handler();
		restore_exception_handler();

		ob_end_clean();

		$error = array(
			"message" => $exception->getMessage(),
			"file" => $exception->getFile(),
			"line" => $exception->getLine(),
			"traces" => $exception->getTrace()
		);
		assign("error", $error);
		render("exception");
		exit(1);
	}
	
	function handle_error($errno, $errstr, $errfile, $errline) {
		restore_error_handler();
		restore_exception_handler();
		
		ob_end_clean();
		
		$trace=debug_backtrace();
		// skip the first 3 stacks as they do not tell the error position
		if (count($trace)>3) $trace = array_slice($trace, 3);

			switch($errno) {
				case E_WARNING:
					$type = 'PHP warning: ';
					break;
				case E_NOTICE:
					$type = 'PHP notice: ';
					break;
				case E_USER_ERROR:
					$type = 'User error: ';
					break;
				case E_USER_WARNING:
					$type = 'User warning: ';
					break;
				case E_USER_NOTICE:
					$type = 'User notice: ';
					break;
				case E_RECOVERABLE_ERROR:
					$type = 'Recoverable error: ';
					break;
				case E_STRICT:
					$type = 'Strict error: ';
					break;
				default:
					$type = 'PHP error: ';
			}
			$error = array(
				'message'=>$type.$errstr,
				'file'=>$errfile,
				'line'=>$errline,
				'traces'=>$trace,
			);
			if(!headers_sent()) header("HTTP/1.0 500 PHP Error");
			assign("error", $error);
			render("exception");
			exit(1);
	}
}
