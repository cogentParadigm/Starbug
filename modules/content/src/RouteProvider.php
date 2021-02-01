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
  }
}
