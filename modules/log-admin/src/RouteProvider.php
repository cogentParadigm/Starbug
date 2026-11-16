<?php
namespace Starbug\Log\Admin;

use Starbug\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Db\Collection\SelectCollection;
use Starbug\Routing\Controller\ViewController;
use Starbug\Routing\Route;
use Starbug\Log\Admin\Collection\AdminErrorLogCollection;
use Starbug\Log\Admin\Display\ErrorLogGrid;
use Starbug\Log\Admin\Display\ErrorLogSearchForm;

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
    $this->addApiRoute($api->addRoute("/error-log/select.json"), "error_log", SelectCollection::class);
  }
}
