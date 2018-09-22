<?php
namespace Starbug\Core;

/**
 * A simple interface for a queue.
 */
class Queue implements QueueInterface {
  public $name;
  protected $items = [];
  protected $db;
  public function __construct(DatabaseInterface $db, $name) {
    $this->db = $db;
    $this->name = $name;
  }
  public function put($item) {
    $item['queue'] = $this->name;
    if (empty($item['status'])) $item['status'] = "pending";
    if (is_array($item['data'])) $item['data'] = json_encode($item['data']);
    $this->db->store("queues", $item);
  }
  public function get() {
    $item = $this->db->query("queues")->condition("queue", $this->name)->condition("status", "pending")->sort("position")->one();
    if ($item && $item['data']) $item['data'] = json_decode($item['data'], true);
    return $item;
  }
  public function release($item) {
    $this->db->query("queues")->condition("id", $item['id'])->set("status", "pending")->update();
  }
  public function remove($item) {
    $this->db->query("queues")->condition("id", $item['id'])->delete();
  }
  public function success($item, $status = "completed") {
    $this->db->query("queues")->condition("id", $item['id'])->set("status", $status)->update();
  }
  public function failure($item, $message = "", $status = "failed") {
    $this->db->query("queues")->condition("id", $item['id'])->set("message", $message)->set("status", $status)->update();
  }
  public function load() {
    $this->items = $this->db->query("queues")->condition("queue", $this->name)->sort("position")->all();
  }
  public function clear() {
    $this->db->query("queues")->condition("queue", $this->name)->delete();
    $this->items = [];
  }
  public function count() {
    return $this->db->query("queues")->condition("queue", $this->name)->condition("status", "pending")->count();
  }
}
