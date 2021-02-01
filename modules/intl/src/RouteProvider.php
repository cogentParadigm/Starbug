<?php
namespace Starbug\Intl;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $address = $this->addCrudRoutes($routes->addRoute("address"), "address");
    $address->setController(null); // no list
    $address->addRoute("/form[/{locale}]", "Starbug\Intl\AddressController");

    $admin = $routes->getRoute("admin");
    $this->addCrudRoutes($admin->addRoute("/countries"), "countries");
    $this->addCrudRoutes($admin->addRoute("/provinces"), "provinces");
  }
}
