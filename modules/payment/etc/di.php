<?php
namespace Starbug\Payment;

use Omnipay\AuthorizeNet\AIMGateway;
use Omnipay\AuthorizeNet\CIMGateway;
use function DI\add;
use function DI\get;
use function DI\autowire;
use DI;
use Psr\Container\ContainerInterface;
use Starbug\Db\DatabaseInterface;
use Starbug\Payment\Script\ProcessSubscriptions;

return [
  "route.providers" => add([
    get("Starbug\Payment\RouteProvider")
  ]),
  'db.schema.migrations' => add([
    get('Starbug\Payment\Migration')
  ]),
  "template.helpers" => add([
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
  'Starbug\Payment\*Interface' => autowire('Starbug\Payment\*'),
  'Starbug\Payment\Cart' => autowire()->constructorParameter('conditions', get('cart_token'))->method("addHooks", get('payment.cart.hooks')),
  'Starbug\Payment\PriceFormatterInterface' => autowire("Starbug\Payment\PriceFormatter")
    ->constructorParameter('locale', get('currency_locale'))
    ->constructorParameter('minorUnit', get('currency_minor_unit')),
  'Starbug\Payment\GatewayInterface' => autowire("Starbug\Payment\Gateway")->constructorParameter("gateway", get('Omnipay\AuthorizeNet\AIMGateway')),
  'Starbug\Payment\TokenGatewayInterface' => autowire("Starbug\Payment\TokenGateway")->constructorParameter("gateway", get('Omnipay\AuthorizeNet\CIMGateway')),
  'Omnipay\AuthorizeNet\AIMGateway' => function (ContainerInterface $c) {
    $settings = $c->get("Starbug\Payment\PaymentSettingsInterface");
    $gateway = new AIMGateway();
    $gateway->setApiLoginId($settings->get("Authorize.Net", 'login_id'));
    $gateway->setTransactionKey($settings->get("Authorize.Net", 'transaction_key'));
    if ($settings->testMode("Authorize.Net")) {
      $gateway->setDeveloperMode(true);
    }
    return $gateway;
  },
  'Omnipay\AuthorizeNet\CIMGateway' => function (ContainerInterface $c) {
    $settings = $c->get("Starbug\Payment\PaymentSettingsInterface");
    $gateway = new CIMGateway();
    $gateway->setApiLoginId($settings->get("Authorize.Net", 'login_id'));
    $gateway->setTransactionKey($settings->get("Authorize.Net", 'transaction_key'));
    if ($settings->testMode("Authorize.Net")) {
      $gateway->setDeveloperMode(true);
    }
    return $gateway;
  },
  'Starbug\Payment\ProductOptionsForm' => autowire()
    ->method('setTableSchema', get('Starbug\Db\Schema\SchemerInterface'))
    ->method("setDatabase", get(DatabaseInterface::class)),
  "Starbug\Payment\ProductsForm" => autowire()
    ->method("setDatabase", get(DatabaseInterface::class)),
  "Starbug\Payment\ProductConfigurationForm" => autowire()
    ->method("setDatabase", get(DatabaseInterface::class)),
];
