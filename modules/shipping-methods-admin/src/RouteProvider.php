<?php
namespace Starbug\ShippingMethods\Admin;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Route;

class RouteProvider extends AdminRouteProvider {
  public function configure(Route $routes) {
    $methods = $this->addCrud($routes, "shipping_methods", [
      "grid" => ShippingMethodsGrid::class,
      "form" => ShippingMethodsForm::class
    ]);
    $this->addCrud($routes, "shipping_rates", [
      "form" => ShippingRatesForm::class
    ]);
    $this->addCrud($routes, "shipping_rates_product_options", [
      "form" => ShippingRatesProductOptionsForm::class
    ]);
    $methods["adminApi"]->setOption("collection", ShippingMethodsAdminCollection::class);
    $methods["selectApi"]->setOption("collection", ShippingMethodsSelectCollection::class);
  }
}
