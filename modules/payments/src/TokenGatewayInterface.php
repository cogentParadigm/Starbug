<?php
namespace Starbug\Payments;

interface TokenGatewayInterface extends GatewayInterface {
  public function createCard($options);
  public function getCard($id = false);
}
