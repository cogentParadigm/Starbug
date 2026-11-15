<?php
namespace Starbug\Content;

use Starbug\Db\Collection\SelectCollection;
use Starbug\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $admin = $routes->getRoute("admin");
    $this->addCrudRoutes($admin->addRoute("/pages"), "pages")->setOptions([
      "grid" => PagesGrid::class,
      "form" => PagesForm::class
    ]);
    $this->addCrudRoutes($admin->addRoute("/categories"), "categories")->setOptions([
      "grid" => CategoriesGrid::class,
      "form" => CategoriesForm::class
    ]);
    $this->addCrudRoutes($admin->addRoute("/tags"), "tags")->setOptions([
      "grid" => TagsGrid::class,
      "form" => TagsForm::class
    ]);

    $routes->addRoute("pages/view/{id:[0-9]+}", "Starbug\Content\PagesController");

    $api = $routes->getRoute("api");

    // Update menu
    $admin->getRoute("/menus")->setOption("form", MenusForm::class);
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
