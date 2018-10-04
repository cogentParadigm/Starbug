<?php
namespace Starbug\Payment;
interface TokenGatewayInterface extends GatewayInterface {
  public function createSubscription($subscription);
  public function updateSubscription($subscription);
  public function cancelSubscription($subcription);
  public function processSubscription($subscription);
}
