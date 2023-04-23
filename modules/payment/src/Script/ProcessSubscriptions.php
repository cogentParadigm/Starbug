<?php
namespace Starbug\Payment\Script;

use Starbug\Db\CollectionFactoryInterface;
use Starbug\Payment\ExpiredSubscriptionsCollection;
use Starbug\Payment\TokenGatewayInterface;

class ProcessSubscriptions {
  public function __construct(TokenGatewayInterface $gateway, CollectionFactoryInterface $collections) {
    $this->gateway = $gateway;
    $this->collections = $collections;
  }
  public function __invoke() {
    $subscriptions = $this->collections->get(ExpiredSubscriptionsCollection::class)->query();
    foreach ($subscriptions as $subscription) {
      $this->gateway->processSubscription($subscription);
    }
  }
}
