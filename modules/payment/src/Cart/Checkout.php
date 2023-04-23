<?php
namespace Starbug\Payment\Cart;

use Starbug\Db\DatabaseInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Core\Operation\Save;
use Starbug\Payment\Cart;

class Checkout extends Save {
  protected $model = "orders";
  public function __construct(DatabaseInterface $db, Cart $cart) {
    $this->db = $db;
    $this->cart = $cart;
  }
  public function handle(array $order, BundleInterface $state): BundleInterface {
    $target = ["id" => $this->cart->get("id"), "billing_same" => $order["billing_same"]];
    if (empty($order['shipping_address'])) {
      $this->db->error("Please enter a shipping address", "shipping_address", $this->model);
    } else {
      $target['shipping_address'] = $order['shipping_address'];
    }
    if ($target["billing_same"]) {
      $target["billing_address"] = "NULL";
    } elseif (empty($order["billing_address"])) {
      $this->db->error("Please enter a billing address", "billing_address", $this->model);
    } else {
      $target['billing_address'] = $order['billing_address'];
    }
    if ($target['id']) {
      $this->db->store($this->model, $target);
    }
    return $this->getErrorState($state);
  }
}
