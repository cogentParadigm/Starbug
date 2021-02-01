<?php
namespace Starbug\Core\Routing\Traits;

use Exception;

trait Routes {
  protected $routes = [];
  public function addRoute($path, $controller = null, $options = []) {
    $this->assertNotExists($path);
    $this->routes[$path] = new static($path, $controller, $options, $this);
    return $this->routes[$path];
  }
  public function replaceRoute($path, $controller, $options = []) {
    unset($this->routes[$path]);
    return $this->addRoute($path, $controller, $options);
  }
  public function getRoute($path) {
    return $this->routes[$path];
  }
  public function getRoutes() {
    return $this->routes;
  }
  public function hasRoute($path) {
    return isset($this->routes[$path]);
  }
  public function removeRoute($path) {
    unset($this->routes[$path]);
  }
  public function clearRoutes() {
    $this->routes = [];
  }
  protected function assertNotExists($path) {
    if (!empty($this->routes[$path])) {
      throw new Exception("Route already exists.");
    }
  }
}