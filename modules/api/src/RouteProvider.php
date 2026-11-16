<?php
namespace Starbug\Api;

use Starbug\Routing\Route;
use Starbug\Routing\RouteProviderInterface;

/**
 * Registers the api route group. Concrete API route providers belong to the
 * modules that expose them.
 */
class RouteProvider implements RouteProviderInterface {
  public function configure(Route $routes) {
    $routes->addRoute("api", null, ["groups" => "admin"]);
  }
}
