<?php
namespace Starbug\Core;

use Starbug\Db\DatabaseInterface;

class ModelTest extends DatabaseTestCase {

  public $model;
  protected $db;
  protected $operations;

  public function setUp(): void {
    parent::setUp();
    global $container;
    $this->db = $container->get(DatabaseInterface::class);
    $this->operations = $container->get("Starbug\Operation\OperationFactoryInterface");
  }

  public function get() {
    $args = array_merge([$this->model], func_get_args());
    return call_user_func_array([$this->db, "get"], $args);
  }

  public function query() {
    $args = array_merge([$this->model], func_get_args());
    return call_user_func_array([$this->db, "query"], $args);
  }

  public function operation($name, $args) {
    $operation = $this->operations->get($name);
    $operation->configure(["model" => $this->model]);
    $operation->execute($args);
    return $operation;
  }
}
