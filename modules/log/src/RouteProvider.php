<?php
namespace Starbug\Log;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Controller\ViewController;
use Starbug\Core\Routing\Route;
use Starbug\Log\Collection\AdminErrorLogCollection;
use Starbug\Log\Display\ErrorLogGrid;
use Starbug\Log\Display\ErrorLogSearchForm;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $admin = $routes->getRoute("admin");
    $admin->addRoute("/error-log", ViewController::class, [
      "model" => "error_log",
      "view" => "admin/list.html",
      "grid" => ErrorLogGrid::class,
      "searchForm" => ErrorLogSearchForm::class
    ]);

    $api = $routes->getRoute("api");
    $this->addAdminApiRoute(
      $api->addRoute("/error-log/admin.{format:csv|json}"),
      "error_log",
      AdminErrorLogCollection::class
    );
    $this->addApiRoute($api->addRoute("/error-log/select.json"), "error_log", "Select");
  }
}
