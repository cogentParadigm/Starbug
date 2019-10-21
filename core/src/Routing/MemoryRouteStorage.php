<?php
namespace Starbug\Core\Routing;

use Starbug\Http\RequestInterface;

class MemoryRouteStorage implements RouteStorageInterface {
  protected $routes = [];
  public function __construct(AccessInterface $access) {
    $this->access = $access;
  }
  public function addRoute($path, $route) {
    $this->routes[$path] = $route;
  }
  public function addRoutes($routes) {
    foreach ($routes as $path => $route) {
      $this->addRoute($path, $route);
    }
  }
  public function getRoute(RequestInterface $request) {
    $route = ["controller" => "main", "action" => "missing", "arguments" => []];
    $paths = $this->expand($request->getPath());
    foreach ($paths as $path) {
      if (!empty($this->routes[$path])) {
        $route = $this->routes[$path] + ["path" => $path];
        if ($this->access->hasAccess($route)) {
          return $route;
        } else {
          $route = ["controller" => "main", "action" => "forbidden", "arguments" => []];
        }
      }
    }
    return $route;
  }
  protected function expand($path) {
    $expanded = [];
    $parts = explode("/", $path);
    foreach ($parts as $idx => $part) {
      if ($idx) $part = $expanded[0].'/'.$part;
      array_unshift($expanded, $part);
    }
    return $expanded;
  }
}
