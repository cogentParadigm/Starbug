<?php
namespace Starbug\Tachyons;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $styles = $routes->addRoute("styleguide/tachyons", [StyleguideController::class, "defaultAction"], [
      "theme" => "tachyons"
    ]);
    $styles->addRoute("/colors", [StyleguideController::class, "colors"]);
    $styles->addRoute("/type", [StyleguideController::class, "type"]);
    $styles->addRoute("/scales", [StyleguideController::class, "scales"]);
    $styles->addRoute("/content", [StyleguideController::class, "content"]);
    $styles->addRoute("/controls", [StyleguideController::class, "controls"]);
    $styles->addRoute("/dgrid", [StyleguideController::class, "dgrid"]);
  }
}
