<?php
namespace Starbug\Content\Admin;

use Starbug\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Content\Admin\Collection\AdminCategoriesCollection;
use Starbug\Content\Admin\Collection\AdminPagesCollection;
use Starbug\Content\Admin\Collection\AdminTagsCollection;
use Starbug\Content\Admin\Collection\MenusTreeCollection;
use Starbug\Content\Admin\Display\CategoriesForm;
use Starbug\Content\Admin\Display\CategoriesGrid;
use Starbug\Content\Admin\Display\MenusForm;
use Starbug\Content\Admin\Display\PagesForm;
use Starbug\Content\Admin\Display\PagesGrid;
use Starbug\Content\Admin\Display\TagsForm;
use Starbug\Content\Admin\Display\TagsGrid;
use Starbug\Db\Collection\SelectCollection;
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

    $api = $routes->getRoute("api");

    $admin->getRoute("/menus")->setOption("form", MenusForm::class);
    $api->getRoute("/menus/tree.json")->setOption("collection", MenusTreeCollection::class);

    $this->addAdminApiRoute(
      $api->addRoute("/categories/admin.{format:csv|json}"),
      "categories",
      AdminCategoriesCollection::class
    );
    $this->addApiRoute($api->addRoute("/categories/select.json"), "categories", SelectCollection::class);

    $this->addAdminApiRoute($api->addRoute("/pages/admin.{format:csv|json}"), "pages", AdminPagesCollection::class);
    $this->addApiRoute($api->addRoute("/pages/select.json"), "pages", SelectCollection::class);

    $this->addAdminApiRoute($api->addRoute("/tags/admin.{format:csv|json}"), "tags", AdminTagsCollection::class);
    $this->addApiRoute($api->addRoute("/tags/select.json"), "tags", SelectCollection::class);
  }
}
