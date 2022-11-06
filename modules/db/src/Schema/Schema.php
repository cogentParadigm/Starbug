<?php
namespace Starbug\Db\Schema;

use Starbug\Bundle\Bundle;

class Schema implements SchemaInterface {
  protected $tables = [];
  protected $rows = [];
  protected $hooks = [];
  public function addHook(HookInterface $hook) {
    $this->hooks[] = $hook;
    return $this;
  }
  public function addTable($table) {
    $args = func_get_args();
    call_user_func_array([$this, "addColumn"], $args);
    return $this;
  }
  public function addColumn($table) {
    $args = func_get_args();
    $table = array_shift($args);
    if (is_array($table)) {
      $ops = $table;
      $table = array_shift($ops);
    } else {
      $ops = [];
    }
    if (empty($this->tables[$table])) {
      $this->tables[$table] = new Table($table);
    }
    foreach ($ops as $k => $v) {
      $this->tables[$table]->setOption($k, $v);
    }
    foreach ($args as $column) {
      $this->tables[$table]->addColumn($column);
      $this->invokeHooks("addColumn", [$column, $this->tables[$table], $this]);
    }
    $this->invokeHooks("addTable", [$this->tables[$table], $ops, $this]);
    return $this;
  }
  public function addRow($table, $keys, $defaults = []) {
    $row = new Bundle(["table" => $table, "keys" => $keys, "defaults" => $defaults]);
    $this->rows[] = $row;
    return $row;
  }
  public function addRows($table, $rows) {
    foreach ($rows as $row) {
      if (!isset($row[1])) {
        $row[1] = [];
      }
      $this->addRow($table, $row[0], $row[1]);
    }
  }
  public function getRows() {
    return $this->rows;
  }
  public function retractRows($table, $retractKeys = []) {
    foreach ($this->rows as $idx => $row) {
      if ($table != $row->get("table")) {
        continue;
      }
      $keys = $row->get("keys");
      $retract = true;
      foreach ($retractKeys as $k => $v) {
        if ($keys[$k] != $v) {
          $retract = false;
        }
      }
      if ($retract) {
        unset($this->rows[$idx]);
      }
    }
  }
  public function getTable($table) {
    $this->invokeHooks("getTable", [$this->tables[$table], $this]);
    return $this->tables[$table];
  }
  public function getTables() {
    foreach ($this->tables as $table => $schema) {
      $this->invokeHooks("getTable", [$this->tables[$table], $this]);
    }
    return $this->tables;
  }
  /**
   * Get the root model of an entity
   *
   * @param string $entity the entity
   *
   * @return string the base model
   */
  public function getEntityRoot($table) {
    $base = $table;
    while ($this->getTable($base)->hasOption("base")) {
      $base = $this->getTable($base)->getOption("base");
    }
    return $base;
  }
  /**
   * Get an array representing the chain of inheritance for an entity
   *
   * @param string $entity the name of the entity
   *
   * @return array the inheritance chain. the first member will be $entity
   */
  public function getEntityChain($table) {
    $chain = [];
    while (!empty($table)) {
      $chain[] = $table;
      $table = $this->getTable($table)->getOption("base");
    }
    return $chain;
  }
  /**
   * Get entity or column info
   *
   * @param string $entity entity name
   * @param string $column column name
   */
  public function getColumn($table, $column = "") {
    $info = [];
    if (!$this->hasTable($table)) {
      return $info;
    }
    while (!empty($table)) {
      $table = $this->getTable($table);
      $columns = $table->getColumns();
      foreach ($columns as $col => $properties) {
        $columns[$col]["entity"] = $table->getName();
      }
      $info = array_merge($columns, $info);
      $table = $table->getOption("base");
    }
    if (!empty($column)) {
      return isset($info[$column]) ? $info[$column] : [];
    }
    return $info;
  }
  public function hasTable($table) {
    return isset($this->tables[$table]);
  }
  public function hasColumn($table, $column) {
    return ($this->hasTable($table) && $this->tables[$table]->hasColumn($column));
  }
  public function dropTable($table) {
    $this->tables[$table]->drop();
    return $this;
  }
  public function dropColumn($table, $column) {
    $this->tables[$table]->dropColumn($column);
    return $this;
  }
  public function addIndex($table, $columns, $options = []) {
    $this->tables[$table]->addIndex($columns, $options);
    return $this;
  }
  public function addUniqueIndex($table, $columns, $options = []) {
    $this->tables[$table]->addUniqueIndex($columns, $options);
    return $this;
  }
  public function dropIndex($table, $columns) {
    $this->tables[$table]->dropIndex($columns);
    return $this;
  }
  public function clean() {
    $this->tables = [];
  }
  protected function invokeHooks($method, $args) {
    foreach ($this->hooks as $hook) {
      call_user_func_array([$hook, $method], $args);
    }
  }
}
