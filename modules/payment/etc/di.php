<?php
use \Interop\Container\ContainerInterface;
return array(
  'routes' => DI\add([
    "cart" => ["controller" => "cart", "title" => "Shopping Cart"],
    "checkout" => ["controller" => "checkout", "title" => "Checkout"],
    "product" => ["controller" => "product"],
    "subscriptions" => ["controller" => "subscriptions", "title" => "Subscriptions"]
  ]),
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Payment\Migration')
  ]),
  'cart_token' => function (ContainerInterface $c) {
    $request = $c->get("Starbug\Core\RequestInterface");
    $url = $c->get("Starbug\Core\URLInterface");
    if ($cid = $request->getCookie("cid")) {
    } else {
      $cid = md5(uniqid(mt_rand(), true));
      setcookie("cid", $cid, 0, $url->build(""), null, false, false);
      $request->setCookie("cid", $cid);
    }
    return ["token" => $cid];
  },
  'currency_locale' => 'en_US.UTF-8',
  'currency_minor_unit' => 2,
  'Starbug\Payment\*Interface' => DI\object('Starbug\Payment\*'),
  'Starbug\Payment\Cart' => DI\object()->constructorParameter('conditions', DI\get('cart_token')),
  'Starbug\Payment\PriceFormatter' => DI\object()
    ->constructorParameter('locale', DI\get('currency_locale'))
    ->constructorParameter('minorUnit', DI\get('currency_minor_unit')),
  'Starbug\Payment\Gateway' => DI\object()->constructorParameter("gateway", DI\get('Omnipay\AuthorizeNet\AIMGateway')),
  'Starbug\Payment\TokenGateway' => DI\object()->constructorParameter("gateway", DI\get('Omnipay\AuthorizeNet\CIMGateway')),
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
  'Starbug\Payment\ProductOptionsForm' => DI\object()->method('setTableSchema', DI\get('Starbug\Db\Schema\SchemerInterface'))
);
