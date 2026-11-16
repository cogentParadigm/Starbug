<?php
namespace Starbug\Content;

use Starbug\Routing\Route;
use Starbug\Routing\RouteProviderInterface;

class RouteProvider implements RouteProviderInterface {

  public function configure(Route $routes) {
    $routes->addRoute("pages/view/{id:[0-9]+}", PagesController::class);
  }
}
