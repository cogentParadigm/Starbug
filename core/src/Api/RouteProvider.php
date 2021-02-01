<?php
namespace Starbug\Core\Api;

use Starbug\Core\Routing\Route;
use Starbug\Core\Routing\RouteProviderInterface;

class RouteProvider implements RouteProviderInterface {
  public function configure(Route $routes) {
    $routes->addRoute("api/{controller}/{action}.{format}", "Starbug\\Core\\ApiRoutingController");
  }
}
