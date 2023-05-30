<?php
namespace Starbug\Payments;

use Omnipay\AuthorizeNet\AIMGateway;
use Omnipay\AuthorizeNet\CIMGateway;
use Psr\Container\ContainerInterface;

use function DI\add;
use function DI\autowire;
use function DI\get;

return [
  "db.schema.migrations" => add([
    get(Migration::class)
  ]),
  "template.helpers" => add([
    "paymentSettings" => SettingsHelper::class
  ]),
  "Starbug\Payments\*Interface" => autowire("Starbug\Payments\*"),
  GatewayInterface::class => autowire(Gateway::class)
    ->constructorParameter("gateway", get(AIMGateway::class)),
  AIMGateway::class => function (ContainerInterface $c) {
    $settings = $c->get(SettingsInterface::class);
    $gateway = new AIMGateway();
    $gateway->setApiLoginId($settings->get("Authorize.Net", "login_id"));
    $gateway->setTransactionKey($settings->get("Authorize.Net", "transaction_key"));
    if ($settings->testMode("Authorize.Net")) {
      $gateway->setDeveloperMode(true);
    }
    return $gateway;
  },
  CIMGateway::class => function (ContainerInterface $c) {
    $settings = $c->get(SettingsInterface::class);
    $gateway = new CIMGateway();
    $gateway->setApiLoginId($settings->get("Authorize.Net", 'login_id'));
    $gateway->setTransactionKey($settings->get("Authorize.Net", 'transaction_key'));
    if ($settings->testMode("Authorize.Net")) {
      $gateway->setDeveloperMode(true);
    }
    return $gateway;
  }
];
