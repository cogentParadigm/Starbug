<?php
namespace Starbug\Notifications\Admin;

use Starbug\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Db\Collection\SelectCollection;
use Starbug\Notifications\Admin\Collection\NotificationsAdminCollection;
use Starbug\Notifications\Admin\Display\NotificationsForm;
use Starbug\Notifications\Admin\Display\NotificationsGrid;
use Starbug\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $admin = $routes->getRoute("admin");
    $this->addCrudRoutes($admin->addRoute("/notifications"), "notifications")->setOptions([
      "grid" => NotificationsGrid::class,
      "form" => NotificationsForm::class
    ]);

    $api = $routes->getRoute("api");
    $this->addAdminApiRoute($api->addRoute("/notifications/admin.{format:csv|json}"), "notifications", NotificationsAdminCollection::class);
    $this->addApiRoute($api->addRoute("/notifications/select.json"), "notifications", SelectCollection::class);
  }
}
