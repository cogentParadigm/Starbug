<?php
namespace Starbug\Core;

/**
 * Request class. interprets the request (URI, $_GET, and $_POST) and serves the appropriate content
 */
class Request implements RequestInterface {
  protected $url;
  protected $language = "en";
  protected $post;
  protected $headers;
  protected $files;
  protected $cookies;

  public function __construct(URLInterface $url) {
    $this->post = new Bundle();
    $this->headers = new Bundle();
    $this->files = new Bundle();
    $this->cookies = new Bundle();
    $this->setURL($url);
  }
  public function setURL(URLInterface $url) {
    $this->url = $url;
    $parts = explode(".", $url->getHost());
    if (count($parts) > 2 && strlen($parts[0]) == 2) $this->setLanguage($parts[0]);
    return $this;
  }
  public function getURL() {
    return $this->url;
  }
  public function setPath($path) {
    $this->url->setPath($path);
    return $this;
  }
  public function getPath() {
    return $this->url->getPath();
  }
  public function setParameter($name, $value) {
    $this->url->setParameter($name, $value);
    return $this;
  }
  public function hasParameter($name) {
    return $this->url->hasParameter($name);
  }
  public function getParameter($name) {
    return $this->url->getParameter($name);
  }
  public function setParameters($parameters) {
    $this->url->setParameters($parameters);
    return $this;
  }
  public function getParameters() {
    return $this->url->getParameters();
  }
  public function getFormat() {
    return $this->url->getFormat();
  }
  public function getComponent($index = 0) {
    return $this->url->getComponent($index);
  }
  public function getComponents() {
    return $this->url->getComponents();
  }
  public function setLanguage($language) {
    $this->language = $language;
    return $this;
  }
  public function getLanguage() {
    return $this->language;
  }
  public function setPost($post) {
    $args = func_get_args();
    call_user_func_array(array($this->post, 'set'), $args);
    return $this;
  }
  public function getPost() {
    $args = func_get_args();
    return call_user_func_array(array($this->post, 'get'), $args);
  }
  public function hasPost($post) {
    $args = func_get_args();
    return call_user_func_array(array($this->post, 'has'), $args);
  }
  public function setHeader($header, $value) {
    $this->headers->set($header, $value);
    return $this;
  }
  public function setHeaders($headers = array()) {
    $this->headers->set($headers);
    return $this;
  }
  public function getHeaders() {
    return $this->headers;
  }
  public function getHeader($name) {
    return $this->headers->get($name);
  }
  public function setFiles($files) {
    $this->files->set($files);
    return $this;
  }
  public function getFiles() {
    return $this->files;
  }
  public function setCookie($name, $value) {
    $this->cookies->set($name, $value);
    return $this;
  }
  public function getCookie($name) {
    $value = $this->cookies->get($name);
    return $this->cookies->get($name);
  }
  public function setCookies($cookies = array()) {
    $this->cookies->set($cookies);
    return $this;
  }
  public function getCookies() {
    return $this->cookies;
  }
}
