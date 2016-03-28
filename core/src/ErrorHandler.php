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
namespace Starbug\Core;
use \Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
/**
 * Error handler
 * @ingroup core
 */
class ErrorHandler {

	protected $out;
	protected $exceptionTemplate;
	protected $logger;
	protected $map = array(
		E_ERROR             => LogLevel::CRITICAL,
		E_WARNING           => LogLevel::WARNING,
		E_PARSE             => LogLevel::ALERT,
		E_NOTICE            => LogLevel::NOTICE,
		E_CORE_ERROR        => LogLevel::CRITICAL,
		E_CORE_WARNING      => LogLevel::WARNING,
		E_COMPILE_ERROR     => LogLevel::ALERT,
		E_COMPILE_WARNING   => LogLevel::WARNING,
		E_USER_ERROR        => LogLevel::ERROR,
		E_USER_WARNING      => LogLevel::WARNING,
		E_USER_NOTICE       => LogLevel::NOTICE,
		E_STRICT            => LogLevel::NOTICE,
		E_RECOVERABLE_ERROR => LogLevel::ERROR,
		E_DEPRECATED        => LogLevel::NOTICE,
		E_USER_DEPRECATED   => LogLevel::NOTICE,
	);
	protected $fatalErrors = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);

	function __construct(TemplateInterface $out, LoggerInterface $logger, $exceptionTemplate = "exception-html") {
		$this->out = $out;
		$this->logger = $logger;
		$this->exceptionTemplate = $exceptionTemplate;
	}

	public function register() {
		set_exception_handler(array($this,'handle_exception'));
		set_error_handler(array($this,'handle_error'), error_reporting());
		register_shutdown_function(array($this, 'handle_shutdown'));
	}

	/**
	 * exception handler
	 */
	function handle_exception($exception) {
		restore_error_handler();
		restore_exception_handler();

		ob_end_clean();

		$error = array(
			"message" => $exception->getMessage(),
			"file" => $exception->getFile(),
			"line" => $exception->getLine()
		);

		$error['traces'] = $exception->getTrace();

		$this->logger->error(sprintf('Uncaught Exception %s: "%s" at %s line %s', get_class($exception), $error['message'], $error['file'], $error['line']), array('exception' => $exception));
		$this->out->render($this->exceptionTemplate, array("error" => $error));
		exit(255);
	}

	/**
	* error handler
	*/
	function handle_error($errno, $errstr, $errfile, $errline) {
		if (0 == error_reporting()) {
			return;
		}
		restore_error_handler();
		restore_exception_handler();

		ob_end_clean();

		$trace=debug_backtrace();
		// skip the first 3 stacks as they do not tell the error position
		if (count($trace)>2) $trace = array_slice($trace, 2);

		$type = self::codeToString($errno).": ";
		$error = array(
			'code' => $errno,
			'message'=>$type.$errstr,
			'file'=>$errfile,
			'line'=>$errline
		);

		$error['traces'] = $trace;

		if (!headers_sent()) header("HTTP/1.0 500 PHP Error");
		if (!in_array($errno, $this->fatalErrors, true)) {
			$level = isset($this->map[$errno]) ? $this->map[$errno] : LogLevel::CRITICAL;
			$this->logger->log($level, $error['message'], $error);
		}
		$this->out->render($this->exceptionTemplate, array("error" => $error));
		exit(1);
	}

	function handle_shutdown() {
		if (is_null($lastError = error_get_last()) === false) {
			if (in_array($lastError['type'], $this->fatalErrors, true)) {
				$this->logger->alert(
					'Fatal Error ('.self::codeToString($lastError['type']).'): '.$lastError['message'],
					array('code' => $lastError['type'], 'message' => $lastError['message'], 'file' => $lastError['file'], 'line' => $lastError['line'])
				);
			}
			ob_end_flush();
		}
	}

	/**
	* renders source around an line. used for exception and error output details
	*/
	function render_source($file, $line, $max) {
		$line--;	// adjust line number to 0-based from 1-based
		if ($line<0 || ($lines=@file($file))===false || ($count=count($lines))<=$line) return '';

		$half = (int)($max/2);
		$start = ($line-$half>0) ? $line-$half : 0;
		$end = ($line+$half<$count) ? $line+$half : $count-1;
		$lineNumberWidth = strlen($end+1);

		$output='';
		for ($i=$start; $i<=$end; ++$i) {
			$isline = $i===$line;
			$code=sprintf("<span class=\"ln".($isline?' error-ln':'')."\">%0{$lineNumberWidth}d</span> %s", $i+1, htmlentities(str_replace("\t", '		', $lines[$i])));
			if (!$isline)
			$output.=$code;
			else $output.='<span class="error">'.$code.'</span>';
		}
		return '<div class="code"><pre>'.$output.'</pre></div>';
	}

	/**
	* converts function arguments from a trace into a readable string
	*/
	function argumentsToString($args) {
		$count=0;

		$isAssoc=$args!==array_values($args);

		foreach ($args as $key => $value) {
			$count++;
			if ($count>=5) {
				if ($count>5) unset($args[$key]);
				else $args[$key]='...';
				continue;
			}

			if (is_object($value)) $args[$key] = get_class($value);
			else if (is_bool($value)) $args[$key] = $value ? 'true' : 'false';
			else if (is_string($value)) {
				if (strlen($value)>64) $args[$key] = '"'.substr($value, 0, 64).'..."';
				else $args[$key] = '"'.$value.'"';
			}
			else if (is_array($value)) $args[$key] = 'array('.ErrorHandler::argumentsToString($value).')';
			else if ($value===null) $args[$key] = 'null';
			else if (is_resource($value)) $args[$key] = 'resource';

			if (is_string($key)) {
				$args[$key] = '"'.$key.'" => '.$args[$key];
			} else if ($isAssoc) {
				$args[$key] = $key.' => '.$args[$key];
			}
		}
		$out = implode(", ", $args);

		return $out;
	}

	private static function codeToString($code) {
		switch ($code) {
			case E_ERROR:
			return 'E_ERROR';
			case E_WARNING:
			return 'E_WARNING';
			case E_PARSE:
			return 'E_PARSE';
			case E_NOTICE:
			return 'E_NOTICE';
			case E_CORE_ERROR:
			return 'E_CORE_ERROR';
			case E_CORE_WARNING:
			return 'E_CORE_WARNING';
			case E_COMPILE_ERROR:
			return 'E_COMPILE_ERROR';
			case E_COMPILE_WARNING:
			return 'E_COMPILE_WARNING';
			case E_USER_ERROR:
			return 'E_USER_ERROR';
			case E_USER_WARNING:
			return 'E_USER_WARNING';
			case E_USER_NOTICE:
			return 'E_USER_NOTICE';
			case E_STRICT:
			return 'E_STRICT';
			case E_RECOVERABLE_ERROR:
			return 'E_RECOVERABLE_ERROR';
			case E_DEPRECATED:
			return 'E_DEPRECATED';
			case E_USER_DEPRECATED:
			return 'E_USER_DEPRECATED';
		}
		return 'Unknown PHP error';
	}
}
