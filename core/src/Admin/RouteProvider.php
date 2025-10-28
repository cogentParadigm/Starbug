<?php
namespace Starbug\Core\Admin;

use Starbug\Admin\Db\Query\AdminCollection;
use Starbug\Db\Collection\SelectCollection;
use Starbug\Routing\Controller\ViewController;
use Starbug\Routing\Route;
use Starbug\Routing\RouteProviderInterface;
use Starbug\Imports\Admin\ImportsGrid;
use Starbug\Routing\Controller;
use Starbug\Users\Operation\UpdateProfile;

class RouteProvider implements RouteProviderInterface {

  public function configure(Route $routes) {
    $routes->addRoute("missing", [Controller::class, "missing"]);
    $routes->addRoute("forbidden", [Controller::class, "forbidden"]);
    // admin group
    $admin = $routes->addRoute("admin", ViewController::class, [
      "title" => "Admin",
      "groups" => "admin",
      "theme" => "storm",
      "menu" => "admin",
      "view" => "admin.html"
    ]);

    // Profile
    $routes->addRoute("profile", ViewController::class, [
      "view" => "profile.html",
      "groups" => "user"
    ])
    ->resolve("id", "Starbug\Routing\Resolvers\UserId")
    ->onPost(UpdateProfile::class);

    // Robots
    $routes->addRoute("robots.{format:txt}", ViewController::class, [
      "view" => "robots.txt"
    ]);
  }
  protected function addCrudRoutes(Route $routes, $model) {
    $routes->setController(ViewController::class);
    $routes->setOption("view", "admin/list.html");
    $routes->setOption("model", $model);
    $routes->setOption("successUrl", $routes->getPath());
    $routes->setOption("cancelUrl", $routes->getPath());
    $routes->setOption("formParams", ["model" => $model]);

    $create = $routes->addRoute("/create", ViewController::class, ["view" => "admin/create.html"])
      ->onPost("Starbug\Core\Operation\Save");

    $update = $routes->addRoute("/update/{id:[0-9]+}", ViewController::class, ["view" => "admin/update.html"])
      ->onPost("Starbug\Core\Operation\Save")
      ->resolve("row", "Starbug\Routing\Resolvers\RowById");

    $routes->addRoute("/delete/{id:[0-9]+}", ViewController::class, ["view" => "admin/delete.html"])
      ->onPost("Starbug\Core\Operation\Delete");

    $routes->addRoute("/import", ViewController::class, [
      "model" => "imports",
      "view" => "admin/list.html",
      "grid" => ImportsGrid::class,
      "listParams" => ["model" => $model]
    ]);

    $this->addXhr($create);
    $this->addXhr($update);

    return $routes;
  }

  protected function addXhr(Route $route) {
    return $route->addRoute(".{format:xhr}", $route->getController(), ["successUrl" => false]);
  }

  protected function addStatefulRedirects(Route $route, $url) {
    $route->setOption("successUrl", $url);
    $route->getRoute("/create")->resolve("row", "Starbug\Routing\Resolvers\RowByInsertId", "outbound");
    $route->getRoute("/update/{id:[0-9]+}")->resolve("row", "Starbug\Routing\Resolvers\RowById");
    return $route;
  }

  protected function addAdminApiRoute(Route $route, $model, $collection = AdminCollection::class) {
    return $this->addApiRoute($route, $model, $collection)
      ->onPost("Starbug\Core\Operation\Save")
      ->onDelete("Starbug\Core\Operation\Delete");
  }

  protected function addApiRoute(Route $route, $model, $collection, $options = []) {
    $route->setController("Starbug\Core\Controller\CollectionController");
    $route->setOptions($options + compact('model', 'collection'));
    return $route;
  }

  protected function addCrud(Route $routes, $model, $listOptions = []) {
    $urlName = str_replace("_", "-", $model);
    $crud = [];

    $admin = $routes->getRoute("admin");
    $crud["list"] = $this->addCrudRoutes($admin->addRoute("/{$urlName}"), $model)->setOptions($listOptions);
    $crud["create"] = $crud["list"]->getRoute("/create");
    $crud["update"] = $crud["list"]->getRoute("/update/{id:[0-9]+}");

    $api = $routes->getRoute("api");
    $crud["adminApi"] = $this->addAdminApiRoute($api->addRoute("/{$urlName}/admin.{format:json|csv}"), $model);
    $crud["selectApi"] = $api->addRoute("/{$urlName}/select.{format:json}");
    $this->addApiRoute($crud["selectApi"], $model, SelectCollection::class);
    return $crud;
  }
}
