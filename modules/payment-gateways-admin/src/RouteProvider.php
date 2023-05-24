<?php
namespace Starbug\Payments\Gateways\Admin;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Routing\Controller\ViewController;
use Starbug\Routing\Resolvers\RowById;
use Starbug\Routing\Route;

class RouteProvider extends AdminRouteProvider {
  public function configure(Route $routes) {
    $gateways = $this->addCrud($routes, "payment_gateways", [
      "grid" => PaymentGatewaysGrid::class,
      "form" => PaymentGatewaysForm::class
    ]);
    $settings = $this->addCrud($routes, "payment_gateway_settings", [
      "grid" => PaymentGatewaySettingsGrid::class,
      "form" => PaymentGatewaySettingsForm::class
    ]);
    $gateways["list"]->addRoute("/settings/{id:[0-9]+}", ViewController::class, [
      "view" => "admin/payment-gateways/settings.html",
      "grid" => PaymentGatewaySettingsGrid::class
    ])->resolve("gateway", RowById::class);
    $this->addStatefulRedirects($settings["list"], "admin/payment-gateways/settings/{{ row.payment_gateway_id }}");
    $settings["adminApi"]->setOption("collection", PaymentGatewaySettingsAdminCollection::class);
  }
}
