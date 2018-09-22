<?php
namespace Starbug\Payment;

use Starbug\Core\CollectionFactoryInterface;

class ProcessSubscriptionsCommand {
  public function __construct(TokenGatewayInterface $gateway, CollectionFactoryInterface $collections) {
    $this->gateway = $gateway;
    $this->collections = $collections;
  }
  public function run($argv) {
    $subscriptions = $this->collections->get("ExpiredSubscriptions")->query();
    foreach ($subscriptions as $subscription) {
      $this->gateway->processSubscription($subscription);
    }
  }
}
