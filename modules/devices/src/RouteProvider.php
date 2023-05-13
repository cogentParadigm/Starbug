<?php
namespace Starbug\Devices;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Route;
use Starbug\Core\SelectCollection;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $api = $routes->getRoute("api");
    $this->addAdminApiRoute($api->addRoute("/devices/admin.{format:csv|json}"), "devices");
    $this->addApiRoute($api->addRoute("/devices/select.json"), "devices", SelectCollection::class);
  }
}
