<?php
namespace Starbug\Core;

/**
 * URLBuilder class. generate absolute URLs from relative paths and modifiers
 */
class URL implements URLInterface {
  protected $scheme;
  protected $host;
  protected $port;
  protected $user;
  protected $password;
  protected $dir = "/";
  protected $components = [];
  protected $path = "";
  protected $format;
  protected $parameters = [];
  protected $fragment;
  protected $absolute = false;

  public function __construct($host = "", $base_directory = "/", $options = []) {
    $this->host = $host;
    $this->dir = $base_directory;
  }

  public function setScheme($scheme) {
    $this->scheme = $scheme;
    return $this;
  }

  public function getScheme() {
    return $this->scheme;
  }

  public function setHost($host) {
    $this->host = $host;
    return $this;
  }

  public function getHost() {
    return $this->host;
  }

  public function setPort($port) {
    $this->port = $port;
    $this->setAbsolute(true);
    return $this;
  }

  public function getPort() {
    return $this->port;
  }

  public function setUser($user) {
    $this->user = $user;
    $this->setAbsolute(true);
    return $this;
  }

  public function getUser() {
    return $this->user;
  }

  public function setPassword($password) {
    $this->password = $password;
    $this->setAbsolute(true);
    return $this;
  }

  public function getPassword() {
    return $this->password;
  }

  public function setDirectory($dir) {
    $this->dir = $dir;
    return $this;
  }

  public function getDirectory() {
    return $this->dir;
  }

  public function getComponent($index = 0) {
    return $this->components[$index];
  }

  public function getComponents() {
    return $this->components;
  }

  public function setPath($path) {
    //if the path contains a query string, split it off
    if (false !== strpos($path, "?")) {
      list($path, $query) = explode("?", $path, 2);
    }

    //if the path includes a format (such as .html, .json, .xml etc..) split it off and set the format for this url
    $file = end(explode("/", $path));
    if (false !== strpos($file, ".")) {
      $this->format = end(explode(".", $file));
      $path = substr($path, 0, -(strlen($this->format)+1));
    }
    $this->path = $path;
    $this->components = explode("/", $path);
    return $this;
  }

  public function getPath() {
    return $this->path;
  }

  public function setFormat($format) {
    $this->format = $format;
    return $this;
  }

  public function getFormat() {
    return $this->format;
  }

  public function setParameter($name, $value) {
    $this->parameters[$name] = $value;
    return $this;
  }

  public function setParameters($parameters = []) {
    foreach ($parameters as $key => $value) {
      $this->setParameter($key, $value);
    }
    return $this;
  }

  public function hasParameter($name) {
    return !empty($this->parameters[$name]);
  }

  public function getParameter($name) {
    return $this->parameters[$name];
  }

  public function getParameters() {
    return $this->parameters;
  }

  public function removeParameter($name) {
    unset($this->parameters[$name]);
    return $this;
  }

  public function clearParameters() {
    $this->parameters = [];
    return $this;
  }

  public function setFragment($fragment) {
    $this->fragment = $fragment;
    return $this;
  }

  public function getFragment() {
    return $this->fragment;
  }

  public function setAbsolute($absolute) {
    $this->absolute = $absolute;
    return $this;
  }

  public function build($path = false, $absolute = false) {
    $url = '';
    if (($absolute || $this->absolute) && isset($this->host)) {
      if (isset($this->scheme)) {
        $url .= $this->scheme . ':';
      }
      $url .= '//';
      if (isset($this->user)) {
        $url .= $this->user;
        if (isset($this->password)) {
          $url .= ':' . $this->password;
        }
        $url .= '@';
      }
      $url .= $this->host;
      if (isset($this->port)) {
        $url .= ':' . $this->port;
      }
    }
    if (false === $path) $path = $this->path;
    $url .= $this->dir . $path;
    if (false === $path && isset($this->format)) {
      $url .= "." . $this->format;
    }
    if (false === $path && !empty($this->parameters)) {
      $url .= '?';
      foreach ($this->parameters as $key => $value) {
        $url .= $key . '=' . $value;
      }
    }
    if (false === $path && isset($this->fragment)) {
      $url .= '#' . $fragment;
    }
    return $url;
  }

  /**
   * Factory method to create an instance from PHP super globals.
   *
   * @param string $base_directory The base_directory parameter passed to the constructor.
   *
   * @return Starbug\Core\URLInterface An instance of this class, matching the HTTP_HOST, REQUEST_URI, and HTTPS values from $_SERVER.
   *
   * @SuppressWarnings(PHPMD.Superglobals)
   */
  public static function createFromSuperGlobals($base_directory = "/") {
    $url = new static($_SERVER['HTTP_HOST'], $base_directory);
    $url->setPath(substr($_SERVER['REQUEST_URI'], strlen($base_directory)));
    $url->setParameters($_GET);
    if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) {
      $url->setScheme("https");
    } else {
      $url->setScheme("http");
    }
    return $url;
  }
}
