<?php
namespace Starbug\Payment;

use Starbug\Core\CollectionFactoryInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Db\Query\EntityInterface;

/**
 * A wrapper around orders intended for mediating shopping cart behavior.
 */
class Cart implements \IteratorAggregate, \ArrayAccess, \Countable {

  protected $order = false;
  protected $lines = [
    "product" => [],
    "shipping" => []
  ];
  protected $hooks = [];

  protected $conditions = [];

  /**
   * Constructor.
   */
  public function __construct(DatabaseInterface $db, EntityInterface $entity, CollectionFactoryInterface $collections, $conditions) {
    $this->db = $db;
    $this->entity = $entity;
    $this->collections = $collections;
    $this->conditions = $conditions;
  }

  public function init($create = true) {
    if (!empty($this->order)) return;
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
    if (!empty($this->order)) {
      foreach ($this->lines as $k => $v) {
        $this->lines[$k] = $this->collections->get(ucwords($k)."Lines")->query(["order" => $this->order["id"]]);
      }
    }
  }

  public function getOrder() {
    $this->init(false);
    return $this->order;
  }

  public function get($property) {
    $this->init(false);
    return empty($this->order) ? null : $this->order[$property];
  }

  public function add($type, $options = []) {
    $this->init(false);
    $options['orders_id'] = $this->order['id'];
    $options["type"] = $type;
    $this->entity->store($type, $options);
  }

  public function offsetSet($offset, $value) {
    $this->init(false);
    if (is_null($offset)) {
      $this->lines['product'][] = $value;
    } else {
      $this->lines['product'][$offset] = $value;
    }
  }

  public function offsetExists($offset) {
    $this->init(false);
    return isset($this->lines['product'][$offset]);
  }

  public function offsetUnset($offset) {
    $this->init(false);
    unset($this->lines['product'][$offset]);
  }

  public function offsetGet($offset) {
    $this->init(false);
    return isset($this->lines['product'][$offset]) ? $this->lines['product'][$offset] : null;
  }

  public function getIterator() {
    $this->init(false);
    return new \ArrayIterator($this->lines['product']);
  }

  public function count() {
    $this->init(false);
    $count = 0;
    foreach ($this->lines['product'] as $line) $count += intval($line['qty']);
    return $count;
  }

  public function getTotal() {
    $this->init(false);
    $total = 0;
    foreach ($this->lines['product'] as $line) $total += intval($line["qty"]) * intval($line["price"]);
    return $total;
  }

  public function addProduct($input) {
    $this->init();
    if (empty($input["qty"])) {
      $input["qty"] = 1;
    }
    foreach ($this->lines["product"] as $line) {
      if ($line["product"] == $input["id"] && empty($line["options"]) && empty($input["options"])) {
        // Product is in cart without options and is being added without options.
          $exists = $this->entity->query("product_lines")->condition("product_lines.id", $line["id"])
          ->condition("product_lines.orders_id", $this->get('id'))->one();
          if ($exists) {
            $this->entity->store("product_lines", ["id" => $line["id"], "qty" => $line["qty"] + $input["qty"]]);
          }
        return $line;
      }
    }
    $product = $this->db->query("products")->condition("products.id", $input['id'])->one();
    $line = [
      "product" => $product['id'],
      "description" => $product['name'],
      "price" => $product['price'],
      "qty" => $input["qty"]
    ];
    $this->invokeHooks("addProduct", [$product, &$line, &$input]);
    $this->add("product_lines", $line);
    $line['id'] = $this->db->getInsertId("product_lines");
    if (!empty($input["options"])) {
      foreach ($input["options"] as $option => $value) {
        $conditions = ["product_lines_id" => $line["id"], "options_id" => $option];
        $exists = $this->db->query("product_lines_options")->conditions($conditions)->one();
        if ($exists) {
          $this->db->store("product_lines_options", ["id" => $exists["id"], "value" => $value]);
        } else {
          $this->db->store("product_lines_options", $conditions + ["value" => $value]);
        }
      }
    }
    return $line;
  }

  public function selectShippingMethod($input) {
    $this->init();
    $method = $this->collections->get("SelectShippingMethods")->one(["order" => $this->order["id"], "id" => $input["id"]]);
    $line = [
      "method" => $method["id"],
      "description" => $method["name"],
      "price" => (string) $method["price"]
    ];
    $line["qty"] = 1;
    if (!empty($this->lines["shipping"])) {
      $line["id"] = $this->lines["shipping"][0]["id"];
    }
    $this->add("shipping_lines", $line);
    $line["id"] = $this->db->getInsertId("shipping_lines");
    return $line;
  }

  public function getShippingMethod() {
    return empty($this->lines["shipping"]) ? false : $this->lines["shipping"][0];
  }

  public function addHook(CartHookInterface $hook) {
    $this->hooks[] = $hook;
  }

  public function addHooks($hooks = []) {
    foreach ($hooks as $hook) {
      $this->addHook($hook);
    }
  }

  protected function invokeHooks($method, $args) {
    $result = $args[0];
    foreach ($this->hooks as $hook) {
      $result = call_user_func_array([$hook, $method], $args);
    }
    return $result;
  }
}
