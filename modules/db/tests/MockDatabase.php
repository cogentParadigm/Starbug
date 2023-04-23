<?php
namespace Starbug\Db\Tests;

use Starbug\Db\DatabaseInterface;
use Starbug\Db\Query\BuilderInterface;

class MockDatabase implements DatabaseInterface {
  protected $prefix = "test_";
  public function get($collection, $conditions = [], $options = []) {
    // Empty function.
  }
  public function query($collection): BuilderInterface {
    // Empty function.
  }
  public function store($name, $fields = [], $from = "auto") {
    // Empty function.
  }
  public function queue($name, $fields = [], $from = "auto", $unshift = false) {
    // Empty function.
  }
  public function storeQueue() {
    // Empty function.
  }
  public function remove($from, $where) {
    // Empty function.
  }
  public function getPrefix() {
    return $this->prefix;
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
  public function setInsertId($table, $id) {
    // Empty function.
  }
  public function getInsertId($table) {
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
  public function quoteIdentifier($str) {
    $char = $this->getIdentifierQuoteCharacter();
    return $char . $str . $char;
  }
  public function getIdentifierQuoteCharacter() {
    return "`";
  }
}
