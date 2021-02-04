<?php
namespace Starbug\Tachyons;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $styles = $routes->addRoute("styleguide", ["Starbug\\Tachyons\\StyleguideController", "defaultAction"]);
    $styles->addRoute("/colors", ["Starbug\\Tachyons\\StyleguideController", "colors"]);
    $styles->addRoute("/type", ["Starbug\\Tachyons\\StyleguideController", "type"]);
    $styles->addRoute("/scales", ["Starbug\\Tachyons\\StyleguideController", "scales"]);
    $styles->addRoute("/content", ["Starbug\\Tachyons\\StyleguideController", "content"]);
    $styles->addRoute("/controls", ["Starbug\\Tachyons\\StyleguideController", "controls"]);
    $styles->addRoute("/dgrid", ["Starbug\\Tachyons\\StyleguideController", "dgrid"]);
  }
}
