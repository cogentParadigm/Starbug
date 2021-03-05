<?php
namespace Starbug\Payment;

class CartHelper {
  public function __construct(Cart $cart) {
    $this->target = $cart;
  }
  public function helper() {
    return $this->target;
  }
}
