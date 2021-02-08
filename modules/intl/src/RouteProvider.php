<?php
namespace Starbug\Intl;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $address = $this->addCrudRoutes($routes->addRoute("address"), "address");
    $address->setController(null); // no list
    $address->setOption("groups", "user");
    $address->addRoute("/form[/{locale}]", "Starbug\Intl\AddressController");

    $admin = $routes->getRoute("admin");
    $this->addCrudRoutes($admin->addRoute("/countries"), "countries");
    $this->addCrudRoutes($admin->addRoute("/provinces"), "provinces");

    $api = $routes->getRoute("api");
    $this->addAdminApiRoute($api->addRoute("/address/admin.json"), "address");
    $this->addApiRoute($api->addRoute("/address/select.json"), "address", "SelectAddress");

    $this->addAdminApiRoute($api->addRoute("/countries/admin.json"), "countries");
    $this->addApiRoute($api->addRoute("/countries/select.json"), "countries", "Select");

    $this->addAdminApiRoute($api->addRoute("/provinces/admin.json"), "provinces");
    $this->addApiRoute($api->addRoute("/provinces/select.json"), "provinces", "Select");
  }
}
