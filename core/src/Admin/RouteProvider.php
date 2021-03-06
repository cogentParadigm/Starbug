<?php
namespace Starbug\Core\Admin;

use Starbug\Core\DatabaseInterface;
use Starbug\Core\Routing\Route;
use Starbug\Core\Routing\RouteProviderInterface;

class RouteProvider implements RouteProviderInterface {

  public function configure(Route $routes) {
    $routes->addRoute("missing", ["Starbug\Core\Controller", "missing"]);
    $routes->addRoute("forbidden", ["Starbug\Core\Controller", "forbidden"]);
    // admin group
    $admin = $routes->addRoute("admin", "Starbug\Core\Controller\ViewController", [
      "title" => "Admin",
      "groups" => "admin",
      "theme" => "storm",
      "view" => "admin.html"
    ]);

    // Settings
    $admin->addRoute("/settings", "Starbug\Core\Controller\ViewController", [
      "view" => "settings.html",
      "model" => "settings"
    ])->onPost("Starbug\Core\Operation\UpdateSettings");

    // Taxonomy
    $terms = $this->addCrudRoutes($admin->addRoute("/taxonomies"), "terms");
    $this->addStatefulRedirects($terms, $terms->getPath()."/taxonomy/{{ row.taxonomy }}");

    $terms->addRoute("/taxonomy/{taxonomy}", "Starbug\Core\Controller\ViewController", [
      "view" => "admin/taxonomies/taxonomy.html"
    ]);

    // Profile
    $routes->addRoute("profile", "Starbug\Core\Controller\ViewController", [
      "view" => "profile.html"
    ])
    ->resolve("id", "Starbug\Core\Routing\Resolvers\UserId");

    // Robots
    $routes->addRoute("robots.{format:txt}", "Starbug\Core\Controller\ViewController", [
      "view" => "robots.txt"
    ]);
  }
  protected function addCrudRoutes(Route $routes, $model) {
    $routes->setController("Starbug\Core\Crud\ListController");
    $routes->setOption("model", $model);
    $routes->setOption("successUrl", $routes->getPath());

    $create = $routes->addRoute("/create", "Starbug\Core\Crud\CreateController")
      ->onPost("Starbug\Core\Operation\Save");

    $update = $routes->addRoute("/update/{id:[0-9]+}", "Starbug\Core\Crud\UpdateController")
      ->onPost("Starbug\Core\Operation\Save")
      ->resolve("row", "Starbug\Core\Routing\Resolvers\RowById");

    $routes->addRoute("/delete/{id:[0-9]+}", "Starbug\Core\Crud\DeleteController")
      ->onPost("Starbug\Core\Operation\Delete");

    $routes->addRoute("/import", "Starbug\Core\Crud\ImportController");

    $this->addXhr($create)
      ->onPost("Starbug\Core\Operation\Save");
    $this->addXhr($update)
      ->onPost("Starbug\Core\Operation\Save")
      ->resolve("row", "Starbug\Core\Routing\Resolvers\RowById");

    return $routes;
  }

  protected function addXhr(Route $route) {
    return $route->addRoute(".{format:xhr}", $route->getController(), ["successUrl" => false]);
  }

  protected function addStatefulRedirects(Route $route, $url) {
    $route->setOption("successUrl", $url);
    $route->getRoute("/create")->resolve("row", "Starbug\Core\Routing\Resolvers\RowByInsertId", "outbound");
    $route->getRoute("/update/{id:[0-9]+}")->resolve("row", "Starbug\Core\Routing\Resolvers\RowById");
    return $route;
  }

  protected function addAdminApiRoute(Route $route, $model, $collection = "Admin") {
    return $this->addApiRoute($route, $model, $collection)
      ->onPost("Starbug\Core\Operation\Save")
      ->onDelete("Starbug\Core\Operation\Delete");
  }

  protected function addApiRoute(Route $route, $model, $collection, $options = []) {
    $route->setController("Starbug\Core\Controller\CollectionController");
    $route->setOptions($options + compact('model', 'collection'));
    return $route;
  }
}
