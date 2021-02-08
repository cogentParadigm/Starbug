<?php
namespace Starbug\Content;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $admin = $routes->getRoute("admin");
    $this->addCrudRoutes($admin->addRoute("/pages"), "pages");
    $this->addCrudRoutes($admin->addRoute("/categories"), "categories");
    $this->addCrudRoutes($admin->addRoute("/tags"), "tags");

    $routes->addRoute("pages/view/{id:[0-9]+}", "Starbug\Content\PagesController");

    $api = $routes->getRoute("api");

    // Categories API
    $this->addAdminApiRoute($api->addRoute("/categories/admin.{format:csv|json}"), "categories", "AdminCategories");
    $this->addApiRoute($api->addRoute("/categories/select.json"), "categories", "Select");

    // Pages API
    $this->addAdminApiRoute($api->addRoute("/pages/admin.{format:csv|json}"), "pages", "AdminPages");
    $this->addApiRoute($api->addRoute("/pages/select.json"), "pages", "Select");

    // Tags API
    $this->addAdminApiRoute($api->addRoute("/tags/admin.{format:csv|json}"), "tags", "AdminTags");
    $this->addApiRoute($api->addRoute("/tags/select.json"), "tags", "Select");
  }
}
