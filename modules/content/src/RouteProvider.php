<?php
namespace Starbug\Content;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;
use Starbug\Core\SelectCollection;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $admin = $routes->getRoute("admin");
    $this->addCrudRoutes($admin->addRoute("/pages"), "pages");
    $this->addCrudRoutes($admin->addRoute("/categories"), "categories");
    $this->addCrudRoutes($admin->addRoute("/tags"), "tags");

    $routes->addRoute("pages/view/{id:[0-9]+}", "Starbug\Content\PagesController");

    $api = $routes->getRoute("api");

    // Update menu
    $api->getRoute("/menus/tree.json")->setOption("collection", MenusTreeCollection::class);

    // Categories API
    $this->addAdminApiRoute(
      $api->addRoute("/categories/admin.{format:csv|json}"),
      "categories",
      AdminCategoriesCollection::class
    );
    $this->addApiRoute($api->addRoute("/categories/select.json"), "categories", SelectCollection::class);

    // Pages API
    $this->addAdminApiRoute($api->addRoute("/pages/admin.{format:csv|json}"), "pages", AdminPagesCollection::class);
    $this->addApiRoute($api->addRoute("/pages/select.json"), "pages", SelectCollection::class);

    // Tags API
    $this->addAdminApiRoute($api->addRoute("/tags/admin.{format:csv|json}"), "tags", AdminTagsCollection::class);
    $this->addApiRoute($api->addRoute("/tags/select.json"), "tags", SelectCollection::class);
  }
}
