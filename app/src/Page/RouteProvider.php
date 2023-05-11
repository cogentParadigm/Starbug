<?php
namespace Starbug\App\Page;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\Routing\Route;
use Starbug\Core\Routing\RouteProviderInterface;

class RouteProvider implements RouteProviderInterface {
  public function configure(Route $routes) {
    $routes->setController("Starbug\App\Page\HomeController");
    $routes->setOption("layout", "home");
    $routes->resolve("menu", function (SessionHandlerInterface $session) {
      if ($session->loggedIn()) {
        return "user";
      }
      return "anonymous";
    });
  }
}
