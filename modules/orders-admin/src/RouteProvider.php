<?php
namespace Starbug\Orders\Admin;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Route;

class RouteProvider extends AdminRouteProvider {
  public function configure(Route $routes) {
    $orders = $this->addCrud($routes, "orders", [
      "grid" => OrdersGrid::class,
      "form" => OrdersForm::class,
      "enableCreateAction" => false
    ]);
    $orders["adminApi"]->setOption("collection", OrdersAdminCollection::class);
    $orders["list"]->addRoute("/view/{id:[0-9]+}", OrdersAdminController::class, [
      "productsGrid" => ProductLinesGrid::class
    ]);
    $api = $routes->getRoute("api");
    $this->addApiRoute($api->addRoute("/product-lines/admin.json"), "product_lines", ProductLinesAdminCollection::class);
  }
}
