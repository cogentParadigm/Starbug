<?php
use Psr\Container\ContainerInterface;
use Starbug\Core\Routing\RoutesHelper;

return [
  'routes' => DI\add(
    [
      "cart" => ["controller" => "cart", "title" => "Shopping Cart"],
      "cart/add" => ["controller" => "cart", "action" => "add", "title" => "Add to Cart"],
      "checkout" => ["controller" => "checkout", "title" => "Checkout"],
      "checkout/guest" => ["controller" => "checkout", "action" => "guest", "title" => "Checkout"],
      "checkout/payment" => ["controller" => "checkout", "action" => "payment", "title" => "Checkout"],
      "checkout/success/{id:[0-9]+}" => ["controller" => "checkout", "action" => "success", "title" => "Checkout"],
      "product/details/{path}" => ["controller" => "product", "action" => "details"],
      "subscriptions" => ["controller" => "subscriptions", "title" => "Subscriptions"],
      "subscriptions/update/{id:[0-9]+}" => ["controller" => "subscriptions", "action" => "update", "title" => "Subscriptions"],
      "subscriptions/payment/{id:[0-9]+}" => ["controller" => "subscriptions", "action" => "payment", "title" => "Subscriptions"],
      "admin/orders/details/{id:[0-9]+}" =>
        RoutesHelper::adminRoute("Starbug\Payment\AdminOrdersController", ["action" => "details"]),
      "admin/payment_gateways/settings/{id:[0-9]+}" =>
        RoutesHelper::adminRoute("Starbug\Payment\AdminPaymentGatewaysController", ["action" => "settings"])
    ]
    + RoutesHelper::crudRoutes("admin/orders", "Starbug\Payment\AdminOrdersController")
    + RoutesHelper::crudiRoutes("admin/payment_gateways", "Starbug\Payment\AdminPaymentGatewaysController")
    + RoutesHelper::crudiRoutes("admin/payment_gateway_settings", "Starbug\Payment\AdminPaymentGatewaySettingsController")
    + RoutesHelper::crudRoutes("admin/product_options", "Starbug\Payment\AdminProductOptionsController")
    + RoutesHelper::crudRoutes("admin/products", "Starbug\Payment\AdminProductsController")
    + RoutesHelper::crudRoutes("admin/product_types", "Starbug\Payment\AdminProductTypesController")
    + RoutesHelper::crudRoutes("admin/shipping_methods", "Starbug\Payment\AdminShippingMethodsController")
    + RoutesHelper::crudRoutes("admin/shipping_rates", "Starbug\Payment\AdminShippingRatesController")
    + RoutesHelper::crudRoutes("admin/shipping_rates_product_options", "Starbug\Payment\AdminShippingRatesProductOptionsController")
  ),
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Payment\Migration')
  ]),
  'cart_token' => function (ContainerInterface $c) {
    $request = $c->get("Psr\Http\Message\ServerRequestInterface");
    $uri = $c->get("Starbug\Http\UriBuilderInterface");
    $cid = $request->getCookieParams()["cid"];
    if (!$cid) {
      $cid = md5(uniqid(mt_rand(), true));
      setcookie("cid", $cid, 0, $uri->build(""), null, false, false);
      // $request->setCookie("cid", $cid);
    }
    return ["token" => $cid];
  },
  'currency_locale' => 'en_US.UTF-8',
  'currency_minor_unit' => 2,
  'payment.cart.hooks' => [],
  'Starbug\Payment\*Interface' => DI\autowire('Starbug\Payment\*'),
  'Starbug\Payment\Cart' => DI\autowire()->constructorParameter('conditions', DI\get('cart_token'))->method("addHooks", DI\get('payment.cart.hooks')),
  'Starbug\Payment\PriceFormatterInterface' => DI\autowire("Starbug\Payment\PriceFormatter")
    ->constructorParameter('locale', DI\get('currency_locale'))
    ->constructorParameter('minorUnit', DI\get('currency_minor_unit')),
  'Starbug\Payment\GatewayInterface' => DI\autowire("Starbug\Payment\Gateway")->constructorParameter("gateway", DI\get('Omnipay\AuthorizeNet\AIMGateway')),
  'Starbug\Payment\TokenGatewayInterface' => DI\autowire("Starbug\Payment\TokenGateway")->constructorParameter("gateway", DI\get('Omnipay\AuthorizeNet\CIMGateway')),
  'Omnipay\AuthorizeNet\AIMGateway' => function (ContainerInterface $c) {
    $settings = $c->get("Starbug\Payment\PaymentSettingsInterface");
    $gateway = new Omnipay\AuthorizeNet\AIMGateway();
    $gateway->setApiLoginId($settings->get("Authorize.Net", 'login_id'));
    $gateway->setTransactionKey($settings->get("Authorize.Net", 'transaction_key'));
    if ($settings->testMode("Authorize.Net")) {
      $gateway->setDeveloperMode(true);
    }
    return $gateway;
  },
  'Omnipay\AuthorizeNet\CIMGateway' => function (ContainerInterface $c) {
    $settings = $c->get("Starbug\Payment\PaymentSettingsInterface");
    $gateway = new Omnipay\AuthorizeNet\CIMGateway();
    $gateway->setApiLoginId($settings->get("Authorize.Net", 'login_id'));
    $gateway->setTransactionKey($settings->get("Authorize.Net", 'transaction_key'));
    if ($settings->testMode("Authorize.Net")) {
      $gateway->setDeveloperMode(true);
    }
    return $gateway;
  },
  'Starbug\Payment\ProductOptionsForm' => DI\autowire()->method('setTableSchema', DI\get('Starbug\Db\Schema\SchemerInterface'))
];
