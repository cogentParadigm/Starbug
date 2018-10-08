<?php
namespace Starbug\Core;

use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase {

  public $model;
  protected $db;
  protected $models;

  public function setUp() {
    global $container;
    $this->db = $container->get("Starbug\Core\DatabaseInterface");
    $this->models = $container->get("Starbug\Core\ModelFactoryInterface");
  }

  public function get() {
    $args = array_merge([$this->model], func_get_args());
    return call_user_func_array([$this->db, "get"], $args);
  }

  public function query() {
    $args = array_merge([$this->model], func_get_args());
    return call_user_func_array([$this->db, "query"], $args);
  }

  public function action() {
    $args = func_get_args();
    $method = array_shift($args);
    return call_user_func_array([$this->models->get($this->model), $method], $args);
  }

  public function __get($name) {
    return $this->models->get($this->model)->$name;
  }
}
