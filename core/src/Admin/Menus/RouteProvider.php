<?php
namespace Starbug\Core\Admin\Menus;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $admin = $routes->getRoute("admin");
    $menus = $this->addCrudRoutes($admin->addRoute("/menus"), "menus");
    $this->addStatefulRedirects($menus, $menus->getPath()."/menu/{{ row.menu }}");

    $menus->addRoute("/menu/{menu}", "Starbug\Core\Controller\ViewController", [
      "view" => "admin/menus/menu.html"
    ]);
  }
}
