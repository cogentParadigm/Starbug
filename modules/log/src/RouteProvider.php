<?php
namespace Starbug\Log;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $admin = $routes->getRoute("admin");
    $admin->addRoute("/error-log", "Starbug\Core\Crud\ListController", ["model" => "error_log"]);

    $api = $routes->getRoute("api");
    $this->addAdminApiRoute($api->addRoute("/error-log/admin.json"), "error_log", "AdminErrorLog");
    $this->addApiRoute($api->addRoute("/error-log/select.json"), "error_log", "Select");
  }
}
