<?php
namespace Starbug\Core\Admin;

use Starbug\Core\DatabaseInterface;
use Starbug\Core\Routing\Route;
use Starbug\Core\Routing\RouteProviderInterface;

class RouteProvider implements RouteProviderInterface {

  public function configure(Route $routes) {
    // admin group
    $admin = $routes->addRoute("admin", "Starbug\Core\Controller\ViewController", [
      "title" => "Admin",
      "groups" => "admin",
      "theme" => "storm",
      "view" => "admin.html"
    ]);

    // Settings
    $admin->addRoute("/settings", "Starbug\Core\Controller\ViewController", [
      "operation" => "Starbug\Core\Operation\UpdateSettings",
      "view" => "settings.html",
      "model" => "settings"
    ]);

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

    $create = $routes->addRoute("/create", "Starbug\Core\Crud\CreateController", [
      "operation" => "Starbug\Core\Operation\Save"
    ]);
    $create->resolve("term", "Starbug\Core\Routing\Resolvers\RowByInsertId");

    $update = $routes->addRoute("/update/{id:[0-9]+}", "Starbug\Core\Crud\UpdateController", [
      "operation" => "Starbug\Core\Operation\Save"
    ]);

    $routes->addRoute("/delete/{id:[0-9]+}", "Starbug\Core\Crud\DeleteController", [
      "operation" => "Starbug\Core\Operation\Delete"
    ]);

    $routes->addRoute("/import", "Starbug\Core\Crud\ImportController");

    $this->addXhr($create);
    $this->addXhr($update);

    return $routes;
  }

  protected function addXhr(Route $route) {
    $route->addRoute(".{format:xhr}", $route->getController(), ["successUrl" => false]);
    return $route;
  }

  protected function addStatefulRedirects(Route $route, $url) {
    $route->setOption("successUrl", $url);
    $route->getRoute("/create")->resolve("row", "Starbug\Core\Routing\Resolvers\RowByInsertId", "outbound");
    $route->getRoute("/update/{id:[0-9]+}")->resolve("row", "Starbug\Core\Routing\Resolvers\RowById");
    return $route;
  }
}
