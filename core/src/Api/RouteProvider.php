<?php
namespace Starbug\Core\Api;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Route;

class RouteProvider extends AdminRouteProvider {
  public function configure(Route $routes) {
    // $routes->addRoute("api/{controller}/{action}.{format}", "Starbug\\Core\\ApiRoutingController");
    $api = $routes->addRoute("api", null, ["groups" => "admin"]);
  }
}
