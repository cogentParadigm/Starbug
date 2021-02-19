<?php
namespace Starbug\Payment;

use Starbug\Core\Admin\RouteProvider as AdminRouteProvider;
use Starbug\Core\Routing\Route;

class RouteProvider extends AdminRouteProvider {

  public function configure(Route $routes) {
    $routes->addRoute("cart", ["Starbug\Payment\CartController", "defaultAction"], ["title" => "Shopping Cart"]);
    $routes->addRoute("cart/add", ["Starbug\Payment\CartController", "add"], ["title" => "Add to Cart"]);
    $routes->addRoute("checkout", ["Starbug\Payment\CheckoutController", "defaultAction"], ["title" => "Checkout"]);
    $routes->addRoute("checkout/guest", ["Starbug\Payment\CheckoutController", "guest"], ["title" => "Checkout"]);
    $routes->addRoute("checkout/payment", ["Starbug\Payment\CheckoutController", "payment"], ["title" => "Checkout"]);
    $routes->addRoute("checkout/success/{id:[0-9]+}", ["Starbug\Payment\CheckoutController", "success"], ["title" => "Checkout"]);
    $routes->addRoute("product/details/{path}", ["Starbug\Payment\ProductController", "details"], ["title" => "Checkout"]);
    $routes->addRoute("subscriptions", "Starbug\Payment\SubscriptionsController");
    $routes->addRoute("subscriptions/update/{id:[0-9]+}", ["Starbug\Payment\SubscriptionsController", "update"]);
    $routes->addRoute("subscriptions/payment/{id:[0-9]+}", ["Starbug\Payment\SubscriptionsController", "payment"]);

    $admin = $routes->getRoute("admin");

    $orders = $this->addCrudRoutes($admin->addRoute("/orders"), "orders");
    $orders->addRoute("/details/{id:[0-9]+}", ["Starbug\Payment\AdminOrdersController", "details"]);

    $gateways = $this->addCrudRoutes($admin->addRoute("/payment-gateways"), "payment_gateways");
    $gateways->addRoute("/settings/{id:[0-9]+}", "Starbug\Core\Controller\ViewController", [
      "view" => "admin/payment_gateways/settings.html"
    ])->resolve("gateway", "Starbug\Core\Routing\Resolvers\RowById");
    $settings = $this->addCrudRoutes($admin->addRoute("/payment-gateway-settings"), "payment_gateway_settings");
    $this->addStatefulRedirects($settings, "admin/payment-gateways/settings/{{ row.payment_gateway_id }}");

    $this->addCrudRoutes($admin->addRoute("/product-options"), "product_options");

    $products = $this->addCrudRoutes($admin->addRoute("/products"), "products");
    $products->getRoute("/create")
      ->setController(["Starbug\Payment\AdminProductsController", "create"]);
    $products->getRoute("/update/{id:[0-9]+}")
      ->setController(["Starbug\Payment\AdminProductsController", "update"]);
    $products->addRoute("/form.{format:xhr}")
      ->setController(["Starbug\Payment\AdminProductsController", "form"]);

    $productTypes = $this->addCrudRoutes($admin->addRoute("/product-types"), "product_types");
    $productTypes->getRoute("/update/{id:[0-9]+}")
      ->setController("Starbug\Core\Controller\ViewController")
      ->setOptions(["view" => "admin/product_types/update.html"]);
    $this->addCrudRoutes($admin->addRoute("/shipping-methods"), "shipping_methods");
    $this->addCrudRoutes($admin->addRoute("/shipping-rates"), "shipping_rates");
    $this->addCrudRoutes($admin->addRoute("/shipping-rates-product-options"), "shipping_rates_product_options");

    $api = $routes->getRoute("api");
    $this->addAdminApiRoute($api->addRoute("/orders/admin.{format:csv|json}"), "orders", "AdminOrders");
    $this->addApiRoute($api->addRoute("/orders/select.json"), "orders", "Select");

    $this->addAdminApiRoute($api->addRoute("/payment-gateways/admin.{format:csv|json}"), "payment_gateways");
    $this->addApiRoute($api->addRoute("/payment-gateways/select.json"), "payment_gateways", "Select");

    $this->addAdminApiRoute($api->addRoute("/payment-gateway-settings/admin.{format:csv|json}"), "payment_gateway_settings", "AdminPaymentGatetwaySettings");
    $this->addApiRoute($api->addRoute("/payment-gateway-settings/select.json"), "payment_gateway_settings", "Select");

    $api->addRoute("/product-lines/admin.json", "Starbug\Payment\ApiProductLinesController", [
      "collection" => "Admin"
    ]);
    $api->addRoute("/product-lines/select.json", "Starbug\Payment\ApiProductLinesController", [
      "collection" => "Select"
    ]);
    $api->addRoute("/product-lines/order.json", "Starbug\Payment\ApiProductLinesController", [
      "collection" => "ProductLines"
    ]);
    $api->addRoute("/product-lines/cart.json", ["Starbug\Payment\ApiProductLinesController", "cart"]);

    $api->addRoute("/shipping-lines/admin.json", "Starbug\Payment\ApiShippingLinesController", [
      "collection" => "Admin"
    ]);
    $api->addRoute("/shipping-lines/select.json", "Starbug\Payment\ApiShippingLinesController", [
      "collection" => "Select"
    ]);
    $api->addRoute("/shipping-lines/order.json", "Starbug\Payment\ApiShippingLinesController", [
      "collection" => "ShippingLines"
    ]);
    $api->addRoute("/shipping-lines/cart.json", ["Starbug\Payment\ApiShippingLinesController", "cart"]);

    $this->addAdminApiRoute($api->addRoute("/product-options/admin.{format:csv|json}"), "product_options", "AdminProductOptions");
    $this->addApiRoute($api->addRoute("/product-options/select.json"), "product_options", "Select");

    $this->addAdminApiRoute($api->addRoute("/products/admin.{format:csv|json}"), "products");
    $this->addApiRoute($api->addRoute("/products/select.json"), "products", "Select");

    $this->addAdminApiRoute($api->addRoute("/product-types/admin.{format:csv|json}"), "product_types");
    $this->addApiRoute($api->addRoute("/product-types/select.json"), "product_types", "Select");

    $api->addRoute("/shipping-methods/admin.{format:csv|json}", "Starbug\Payment\ApiShippingMethodsController", [
      "collection" => "AdminShippingMethods"
    ]);
    $api->addRoute("/shipping-methods/select.json", ["Starbug\Payment\ApiShippingMethodsController", "select"]);

    $this->addAdminApiRoute($api->addRoute("/shipping-rates/admin.{format:csv|json}"), "shipping_rates");
    $this->addApiRoute($api->addRoute("/shipping-rates/select.json"), "shipping_rates", "Select");

    $this->addAdminApiRoute($api->addRoute("/shipping-rates-product-options/admin.{format:csv|json}"), "shipping_rates_product_options");
    $this->addApiRoute($api->addRoute("/shipping-rates-product-options/select.json"), "shipping_rates_product_options", "Select");
  }
}
