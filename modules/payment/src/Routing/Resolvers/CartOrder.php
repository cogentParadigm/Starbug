<?php
namespace Starbug\Payment\Routing\Resolvers;

use Starbug\Payment\Cart;

class CartOrder {
  public function __invoke(Cart $cart) {
    if (count($cart) > 0) {
      return $cart->getOrder();
    }
    return [];
  }
}
