<?php
namespace Starbug\Core;

/**
 * Response class
 */
class Response implements ResponseInterface {

  protected $headers;
  protected $cookies;
  protected $code;
  protected $content_type = "text/html";
  protected $charset = "UTF-8";
  protected $callable = false;

  protected $codes = [
    200 => 'OK',
    301 => 'Moved Permanently',
    302 => 'Found',
    304 => 'Not Modified',
    400 => 'Bad Request',
    403 => 'Forbidden',
    404 => 'Not Found',
    408 => 'Request Timeout',
    429 => 'Too Many Requests',
    500 => 'Internal Server Error',
    503 => 'Service Unavailable'
  ];

  public $theme;
  public $template = "html.html";
  public $layout = "views";
  public $styles;
  public $scripts;

  protected $output;

  public function __construct(TemplateInterface $output, $status_code = 200, $headers = []) {
    $this->output = $output;
    $this->code = $status_code;
    $this->headers = new Bundle($headers);
    $this->cookies = new Bundle();
    $this->styles = new Bundle();
    $this->scripts = new Bundle();
  }

  public function getHeaders() {
    return $this->headers;
  }
  public function setHeaders($headers = []) {
    foreach ($headers as $name => $value) {
      $this->setHeader($name, $value);
    }
    return $this;
  }
  public function getHeader($name) {
    return $this->headers->get($name);
  }
  public function setHeader($name, $value = null) {
    $this->headers->set($name, $value);
    return $this;
  }
  public function getCookies() {
    return $this->cookies;
  }
  public function setCookies($cookies = []) {
    foreach ($cookies as $name => $value) {
      $this->setCookie($name, $value);
    }
    return $this;
  }
  public function getCookie($name) {
    return $this->cookies->get($name);
  }
  public function setCookie($name, $value = null) {
    $this->cookies->set($name, $value);
    return $this;
  }
  public function getCode() {
    return $this->code;
  }
  public function setCode($code) {
    $this->code = $code;
    return $this;
  }
  public function getContentType() {
    return $this->content_type;
  }
  public function setContentType($type) {
    $this->content_type = $type;
    return $this;
  }
  public function getCharset() {
    return $this->charset;
  }
  public function setCharset($charset) {
    $this->charset = $charset;
    return $this;
  }
  public function getContent() {
    return $this->content;
  }
  public function setContent($content) {
    $this->content = $content;
    return $this;
  }
  public function getCallable() {
    return $this->callable;
  }
  public function setCallable(callable $callable) {
    $this->callable = $callable;
    return $this;
  }
  public function getTemplate() {
    return $this->template;
  }
  public function setTemplate($template) {
    $this->template = $template;
    return $this;
  }
  public function getScripts() {
    return $this->scripts;
  }
  public function setScripts($scripts = []) {
    foreach ($scripts as $name => $script) {
      $this->setScript($name, $script);
    }
    return $this;
  }
  public function getScript($name) {
    return $this->scripts->get($name);
  }
  public function setScript($name, $value = null) {
    $this->scripts->set($name, $value);
    return $this;
  }
  public function sendHeaders() {
    $code = $this->code;
    if (isset($this->codes[$code])) $code .= " ".$this->codes[$code];
    header("HTTP/1.1 ".$code);
    header('Content-Type: '.$this->content_type.'; charset='.$this->charset);
    header('Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0, max-age=0');
    foreach ($this->headers as $name => $value) {
      header($name.": ".$value);
    }
  }

  public function sendCookies() {
    foreach ($this->cookies as $name => $cookie) {
      setcookie($name, $cookie['value'], $cookie['expires'], $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']);
    }
  }

  public function sendContent() {
    $this->output->render($this->template, ["response" => $this]);
  }

  public function send() {
    ob_start();
    $this->sendHeaders();
    $this->sendCookies();
    if (false === $this->callable) $this->sendContent();
    ob_end_flush();
    if (false !== $this->callable) call_user_func($this->callable);
  }

  /**
   * Sends a 404 and sets the payload, path, and uri
   */
  public function missing() {
    $this->code = 404;
  }

  /**
   * Sends a 403 and sets the payload, path, and uri
   */
  public function forbidden() {
    $this->code = 403;
  }

  public function js($mid) {
    $this->scripts->set($mid, $mid);
  }
}
