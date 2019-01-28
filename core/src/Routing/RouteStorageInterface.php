<?php
namespace Starbug\Core\Routing;

use Starbug\Http\RequestInterface;

interface RouteStorageInterface {
  public function addRoute($path, $route);
  public function addRoutes($routes);
  public function getRoute(RequestInterface $request);
}
