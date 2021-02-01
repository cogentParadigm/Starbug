<?php
namespace Starbug\Core\Routing;

use FastRoute\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;

class FastRouteStorage implements RouteStorageInterface {
  /**
   * FastRoute Dispatcher
   *
   * @var Dispatcher
   */
  protected $dispatcher;
  protected $routes = [];
  public function __construct(Dispatcher $dispatcher, AccessInterface $access) {
    $this->dispatcher = $dispatcher;
    $this->access = $access;
  }
  public function getRoute(ServerRequestInterface $request) {
    $path = $request->getUri()->getPath();
    $routeInfo = $this->dispatcher->dispatch("GET", $path);
    if ($routeInfo[0] == Dispatcher::FOUND) {
      $route = $routeInfo[1];
      $vars = $routeInfo[2];
      if ($this->access->hasAccess($route)) {
        $route->setOptions($vars);
        return $route;
      } else {
        return new Route($route->getPath(), ["Starbug\\Core\\MainController", "forbidden"]);
      }
    }
    return new Route($path, ["Starbug\\Core\\MainController", "missing"]);
  }
}
