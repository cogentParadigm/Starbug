<?php
namespace Starbug\Log;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Controller\ViewController;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $admin = $routes->getRoute("admin");
    $admin->addRoute("/error-log", ViewController::class, [
      "model" => "error_log",
      "view" => "admin/list.html"
    ]);

    $api = $routes->getRoute("api");
    $this->addAdminApiRoute($api->addRoute("/error-log/admin.{format:csv|json}"), "error_log", "AdminErrorLog");
    $this->addApiRoute($api->addRoute("/error-log/select.json"), "error_log", "Select");
  }
}
