<?php
namespace Starbug\Db\Tests;

use Starbug\Core\DatabaseInterface;

class MockDatabase implements DatabaseInterface {
  protected $prefix = "test_";
  public function get($collection, $conditions = [], $options = []) {
    // Empty function.
  }
  public function query($collection) {
    // Empty function.
  }
  public function store($name, $fields = [], $from = "auto") {
    // Empty function.
  }
  public function queue($name, $fields = [], $from = "auto", $unshift = false) {
    // Empty function.
  }
  public function store_queue() {
    // Empty function.
  }
  public function remove($from, $where) {
    // Empty function.
  }
  public function prefix($table) {
    return $this->prefix.$table;
  }
  public function setDatabase($name) {
    // Empty function.
  }
  public function exec($statement) {
    // Empty function.
  }
  public function prepare($statement) {
    // Empty function.
  }
  public function lastInsertId() {
    // Empty function.
  }
  public function errors($key = "", $values = false) {
    // Empty function.
  }
  public function error($error, $field = "global", $scope = "global") {
    // Empty function.
  }
  public function success($model, $action) {
    // Empty function.
  }
  public function failure($model, $action) {
    // Empty function.
  }
}
