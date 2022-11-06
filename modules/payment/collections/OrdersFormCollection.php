<?php
namespace Starbug\Payment;

use Starbug\Core\FormCollection;
use Starbug\Core\DatabaseInterface;

class OrdersFormCollection extends FormCollection {
  public function __construct(DatabaseInterface $db, Cart $cart) {
    $this->db = $db;
    $this->cart = $cart;
  }
  public function build($query, $ops) {
    if (empty($ops["action"])) {
      $ops["action"] = "checkout";
    }
    if (in_array($ops["action"], ["checkout", "payment"])) {
      if ($ops["id"] !== $this->cart->get("id")) {
        $query->action($ops["action"], "orders");
      }
    } else {
      $query->action($ops["action"], "orders");
    }
    $query->condition("orders.id", $ops["id"]);
    if (!empty($ops["subscription"])) {
      $query->join("subscriptions")->on("subscriptions.orders_id=orders.id");
      $query->condition("subscriptions.id", $ops["subscription"]);
      $query->select("subscriptions.card");
    }
    return $query;
  }
}
