<?php
namespace Starbug\Payment;

use Starbug\Core\DatabaseInterface;
use Starbug\Core\Table;
use Starbug\Db\Schema\SchemerInterface;

class ProductLines extends Table {

  public function __construct(DatabaseInterface $db, SchemerInterface $schemer, Cart $cart) {
    parent::__construct($db, $schemer);
    $this->cart = $cart;
  }

  public function update($lines) {
    if (count($this->cart)) {
      foreach ($lines as $id => $qty) {
        $line = $this->query()->condition("product_lines.id", $id)
        ->condition("product_lines.orders_id", $this->cart->get('id'))->one();
        if ($line) {
          $this->store(["id" => $id, "qty" => $qty]);
        }
      }
    } else {
      $this->error("You have no items in your cart", "global");
    }
  }
  public function delete($line) {
    if (count($this->cart)) {
      $line = $this->query()->condition("product_lines.id", $line['id'])
        ->condition("product_lines.orders_id", $this->cart->get('id'))->one();
      if ($line) {
        $this->remove($line['id']);
      }
    } else {
      $this->error("You have no items in your cart", "global");
    }
  }
  public function post($action, $data = []) {
    $this->action = $action;
    if ($action == "update" || $action == "delete") {
      $this->$action($data);
      return true;
    } else {
      return parent::post($action, $data);
    }
  }
}
