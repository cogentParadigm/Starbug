<?php
namespace Starbug\Payment;

use DI;
use Psr\Container\ContainerInterface;
use Starbug\Payment\Script\ProcessSubscriptions;

return [
  "route.providers" => DI\add([
    DI\get("Starbug\Payment\RouteProvider")
  ]),
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Payment\Migration')
  ]),
  "template.helpers" => DI\add([
    "cart" => CartHelper::class,
    "paymentSettings" => PaymentSettingsHelper::class,
    "priceFormatter" => PriceFormatterHelper::class
  ]),
  'cart_token' => function (ContainerInterface $c) {
    $request = $c->get("Psr\Http\Message\ServerRequestInterface");
    $uri = $c->get("Starbug\Http\UriBuilderInterface");
    $cid = $request->getCookieParams()["cid"] ?? false;
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
  "scripts.process-subscriptions" => ProcessSubscriptions::class,
  'Starbug\Payment\*Interface' => DI\autowire('Starbug\Payment\*'),
  'Starbug\Payment\Cart' => DI\autowire()->constructorParameter('conditions', DI\get('cart_token'))->method("addHooks", DI\get('payment.cart.hooks')),
  'Starbug\Payment\PriceFormatterInterface' => DI\autowire("Starbug\Payment\PriceFormatter")
    ->constructorParameter('locale', DI\get('currency_locale'))
    ->constructorParameter('minorUnit', DI\get('currency_minor_unit')),
  'Starbug\Payment\GatewayInterface' => DI\autowire("Starbug\Payment\Gateway")->constructorParameter("gateway", DI\get('Omnipay\AuthorizeNet\AIMGateway')),
  'Starbug\Payment\TokenGatewayInterface' => DI\autowire("Starbug\Payment\TokenGateway")->constructorParameter("gateway", DI\get('Omnipay\AuthorizeNet\CIMGateway')),
  'Omnipay\AuthorizeNet\AIMGateway' => function (ContainerInterface $c) {
    $settings = $c->get("Starbug\Payment\PaymentSettingsInterface");
    $gateway = new \Omnipay\AuthorizeNet\AIMGateway();
    $gateway->setApiLoginId($settings->get("Authorize.Net", 'login_id'));
    $gateway->setTransactionKey($settings->get("Authorize.Net", 'transaction_key'));
    if ($settings->testMode("Authorize.Net")) {
      $gateway->setDeveloperMode(true);
    }
    return $gateway;
  },
  'Omnipay\AuthorizeNet\CIMGateway' => function (ContainerInterface $c) {
    $settings = $c->get("Starbug\Payment\PaymentSettingsInterface");
    $gateway = new \Omnipay\AuthorizeNet\CIMGateway();
    $gateway->setApiLoginId($settings->get("Authorize.Net", 'login_id'));
    $gateway->setTransactionKey($settings->get("Authorize.Net", 'transaction_key'));
    if ($settings->testMode("Authorize.Net")) {
      $gateway->setDeveloperMode(true);
    }
    return $gateway;
  },
  'Starbug\Payment\ProductOptionsForm' => DI\autowire()
    ->method('setTableSchema', DI\get('Starbug\Db\Schema\SchemerInterface'))
    ->method("setDatabase", DI\get("Starbug\Core\DatabaseInterface")),
  "Starbug\Payment\ProductsForm" => DI\autowire()
    ->method("setDatabase", DI\get("Starbug\Core\DatabaseInterface")),
  "Starbug\Payment\ProductConfigurationForm" => DI\autowire()
    ->method("setDatabase", DI\get("Starbug\Core\DatabaseInterface")),
];
