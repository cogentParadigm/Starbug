<?php
namespace Starbug\Core;

use \Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Error handler.
 */
class ErrorHandler {

  protected $out;
  protected $exceptionTemplate = "exception.txt";
  protected $logger;
  protected $map = [
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
  ];
  protected $fatalErrors = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
  protected $contentOnly = true;

  public function __construct(ResponseInterface $response, LoggerInterface $logger) {
    $this->response = $response;
    $this->logger = $logger;
  }

  public function setTemplate($template) {
    $this->exceptionTemplate = $template;
  }

  public function setContentOnly($contentOnly) {
    $this->contentOnly = $contentOnly;
  }

  public function register() {
    set_exception_handler([$this, 'handleException']);
    set_error_handler([$this, 'handleError'], error_reporting());
    register_shutdown_function([$this, 'handleShutdown']);
  }

  /**
   * Exception handler
   */
  public function handleException($exception) {
    restore_error_handler();
    restore_exception_handler();

    ob_end_clean();

    $error = [
      "message" => $exception->getMessage(),
      "file" => $exception->getFile(),
      "line" => $exception->getLine()
    ];

    $error['traces'] = $exception->getTrace();

    $this->logger->error(sprintf('Uncaught Exception %s: "%s" at %s line %s', get_class($exception), $error['message'], $error['file'], $error['line']), ['exception' => $exception]);
    $this->response->setCode(500);
    $this->response->setHeader("Cache-Control", "no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0");
    $this->response->capture($this->exceptionTemplate, ["error" => $error, "handler" => $this], ["scope" => "templates"]);
    if ($this->contentOnly) {
      $this->response->sendContent();
    } else {
      $this->response->send();
    }
  }

  /**
   * Error handler.
   */
  public function handleError($errno, $errstr, $errfile, $errline) {
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
    $error = [
      'code' => $errno,
      'message'=>$type.$errstr,
      'file'=>$errfile,
      'line'=>$errline
    ];

    $error['traces'] = $trace;

    if (!headers_sent()) header("HTTP/1.0 500 PHP Error");
    if (!in_array($errno, $this->fatalErrors, true)) {
      $level = isset($this->map[$errno]) ? $this->map[$errno] : LogLevel::CRITICAL;
      $this->logger->log($level, $error['message'], $error);
    }
    $this->response->setCode(500);
    $this->response->setHeader("Cache-Control", "no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0");
    $this->response->capture($this->exceptionTemplate, ["error" => $error, "handler" => $this], ["scope" => "templates"]);
    if ($this->contentOnly) {
      $this->response->sendContent();
    } else {
      $this->response->send();
    }
  }

  public function handleShutdown() {
    if (is_null($lastError = error_get_last()) === false) {
      if (in_array($lastError['type'], $this->fatalErrors, true)) {
        $this->logger->alert(
          'Fatal Error ('.self::codeToString($lastError['type']).'): '.$lastError['message'],
          ['code' => $lastError['type'], 'message' => $lastError['message'], 'file' => $lastError['file'], 'line' => $lastError['line']]
        );
      }
      ob_end_flush();
    }
  }

  /**
   * Renders source around an line. used for exception and error output details
   */
  public static function renderSource($file, $line, $max) {
    $line--; // adjust line number to 0-based from 1-based
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
   * Converts function arguments from a trace into a readable string
   */
  public static function argumentsToString($args) {
    $count=0;

    $isAssoc=$args!==array_values($args);

    foreach ($args as $key => $value) {
      $count++;
      if ($count>=5) {
        if ($count>5) unset($args[$key]);
        else $args[$key]='...';
        continue;
      }

      if (is_object($value)) {
        $args[$key] = get_class($value);
      } elseif (is_bool($value)) {
        $args[$key] = $value ? 'true' : 'false';
      } elseif (is_string($value)) {
        if (strlen($value)>64) {
          $args[$key] = '"'.substr($value, 0, 64).'..."';
        } else {
          $args[$key] = '"'.$value.'"';
        }
      } elseif (is_array($value)) {
        $args[$key] = 'array('.ErrorHandler::argumentsToString($value).')';
      } elseif ($value === null) {
        $args[$key] = 'null';
      } elseif (is_resource($value)) {
        $args[$key] = 'resource';
      }

      if (is_string($key)) {
        $args[$key] = '"'.$key.'" => '.$args[$key];
      } elseif ($isAssoc) {
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
