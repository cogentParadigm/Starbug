<?php
namespace Starbug\Payment;

use Starbug\Core\ShippingMethodsModel;

class ShippingMethods extends ShippingMethodsModel {
  public function add($method) {
    $method = $this->cart->selectShippingMethod($method);
  }
}
