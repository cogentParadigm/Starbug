<?php
namespace Starbug\Menus;

use Starbug\Db\Operation\Save;
use Starbug\Db\Operation\Delete;
use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Controller\ViewController;
use Starbug\Routing\Route;
use Starbug\Menus\Collection\MenusAdminCollection;
use Starbug\Menus\Collection\MenusTreeCollection;
use Starbug\Menus\Display\MenusForm;
use Starbug\Menus\Display\MenusGrid;
use Starbug\Menus\Display\MenusTreeGrid;
use Starbug\Menus\Operation\DeleteMenus;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $menus = $this->addCrud($routes, "menus", [
      "grid" => MenusGrid::class,
      "form" => MenusForm::class
    ]);
    $this->addStatefulRedirects($menus["list"], $menus["list"]->getPath()."/menu/{{ row.menu }}");

    $menus["list"]->addRoute("/menu/{menuName}", ViewController::class, [
      "view" => "admin/menus/menu.html",
      "grid" => MenusTreeGrid::class
    ]);

    $menus["list"]->setOptions([
      "grid" => MenusGrid::class,
      "form" => MenusForm::class
    ]);

    $menus["adminApi"]
      ->setOption("collection", MenusAdminCollection::class)
      ->onDelete(DeleteMenus::class);

    $api = $routes->getRoute("api");
    $this->addApiRoute($api->addRoute("/menus/tree.json"), "menus", MenusTreeCollection::class)
      ->onPost(Save::class)
      ->onDelete(Delete::class);
  }
}
