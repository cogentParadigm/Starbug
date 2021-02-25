<?php
namespace Starbug\Payment\Routing\Resolvers;

use Starbug\Core\Routing\Route;
use Starbug\Payment\Cart;

class CartOrder {
  public function __invoke(Route $route, Cart $cart) {
    if (count($cart) > 0) {
      return $cart->getOrder();
    }
    $route->notFound();
    return [];
  }
}
