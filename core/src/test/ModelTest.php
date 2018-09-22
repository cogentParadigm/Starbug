<?php
namespace Starbug\Core;

class ModelTest extends \PHPUnit_Framework_TestCase {

  public $model;
  protected $db;
  protected $models;

  public function setUp() {
    global $container;
    $this->db = $container->get("Starbug\Core\DatabaseInterface");
    $this->models = $container->get("Starbug\Core\ModelFactoryInterface");
  }

  protected function get() {
    $args = array_merge([$this->model], func_get_args());
    return call_user_func_array([$this->db, "get"], $args);
  }

  protected function query() {
    $args = array_merge([$this->model], func_get_args());
    return call_user_func_array([$this->db, "query"], $args);
  }

  protected function action() {
    $args = func_get_args();
    $method = array_shift($args);
    return call_user_func_array([$this->models->get($this->model), $method], $args);
  }

  protected function __get($name) {
    return $this->models->get($this->model)->$name;
  }
}
