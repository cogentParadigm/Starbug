<?php
namespace Starbug\Orders;

use Starbug\Db\DatabaseInterface;

/**
 * A wrapper around orders intended for mediating shopping cart behavior.
 */
class Cart {

  protected $order = false;
  protected $conditions = [];

  /**
   * Constructor.
   */
  public function __construct(DatabaseInterface $db, $conditions) {
    $this->db = $db;
    $this->conditions = $conditions;
  }

  public function init($create = true) {
    if (!empty($this->order)) {
      return;
    }
    $this->load();
    if (empty($this->order) && $create) {
      $this->db->store("orders", $this->conditions);
      $this->load();
    }
  }

  public function reset($load = true, $create = true) {
    $this->order = false;
    if ($load) {
      $this->init($create);
    }
  }

  public function setConditions($conditions) {
    $this->conditions = $conditions;
  }

  public function load($conditions = []) {
    if (empty($conditions)) {
      $conditions = $this->conditions;
    }
    if (empty($conditions["order_status"])) {
      $conditions["order_status"] = "cart";
    }
    $order = $this->db->query("orders")->conditions($conditions)->one();
    $this->setOrder($order);
  }

  public function setOrder($order) {
    $this->order = $order;
  }

  public function getOrder() {
    $this->init(false);
    return $this->order;
  }

  public function get($property) {
    $this->init(false);
    return empty($this->order) ? null : $this->order[$property];
  }
}
