<?php
namespace Starbug\Emails;

use Starbug\Db\Collection\SelectCollection;
use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $admin = $routes->getRoute("admin");
    $this->addCrudRoutes($admin->addRoute("/email-templates"), "email_templates");

    $api = $routes->getRoute("api");
    $this->addAdminApiRoute($api->addRoute("/email-templates/admin.{format:csv|json}"), "email_templates");
    $this->addApiRoute($api->addRoute("/email-templates/select.json"), "email_templates", SelectCollection::class);
  }
}
