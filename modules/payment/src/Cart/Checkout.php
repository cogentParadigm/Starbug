<?php
namespace Starbug\Payment\Cart;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\Operation\Save;
use Starbug\Payment\Cart;

class Checkout extends Save {
  protected $model = "orders";
  public function __construct(ModelFactoryInterface $models, Cart $cart) {
    $this->models = $models;
    $this->cart = $cart;
  }
  public function handle(array $order, BundleInterface $state): BundleInterface {
    $target = ["id" => $this->cart->get("id"), "billing_same" => $order["billing_same"]];
    if (empty($order['shipping_address'])) {
      $this->error("Please enter a shipping address", "shipping_address");
    } else {
      $target['shipping_address'] = $order['shipping_address'];
    }
    if ($target["billing_same"]) {
      $target["billing_address"] = "NULL";
    } elseif (empty($order["billing_address"])) {
      $this->error("Please enter a billing address", "billing_address");
    } else {
      $target['billing_address'] = $order['billing_address'];
    }
    if ($target['id']) {
      $this->store($target);
    }
    return $this->getErrorState($state);
  }
}
