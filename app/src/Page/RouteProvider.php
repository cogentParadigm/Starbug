<?php
namespace Starbug\App\Page;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Routing\Route;
use Starbug\Routing\RouteProviderInterface;

class RouteProvider implements RouteProviderInterface {
  public function configure(Route $routes) {
    $routes->setController("Starbug\App\Page\HomeController");
    $routes->setOption("layout", "home");
    $routes->resolve("menu", function (SessionHandlerInterface $session, $menu = false) {
      if ($menu) {
        return $menu;
      }
      if ($session->loggedIn()) {
        return "user";
      }
      return "anonymous";
    });
  }
}
