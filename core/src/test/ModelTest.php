<?php
namespace Starbug\Core;

class ModelTest extends \PHPUnit_Framework_TestCase {

  public $model;
  protected $db;
  protected $models;

  function setUp() {
    global $container;
    $this->db = $container->get("Starbug\Core\DatabaseInterface");
    $this->models = $container->get("Starbug\Core\ModelFactoryInterface");
  }

  function get() {
    $args = array_merge(array($this->model), func_get_args());
    return call_user_func_array(array($this->db, "get"), $args);
  }

  function query() {
    $args = array_merge(array($this->model), func_get_args());
    return call_user_func_array(array($this->db, "query"), $args);
  }

  function action() {
    $args = func_get_args();
    $method = array_shift($args);
    return call_user_func_array(array($this->models->get($this->model), $method), $args);
  }

  function __get($name) {
    return $this->models->get($this->model)->$name;
  }
}
