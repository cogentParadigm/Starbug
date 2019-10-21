<?php
namespace Starbug\Db\Query\Traits;

use Starbug\Db\Query\Table;

trait Tables {
  protected $tables = [];
  protected $baseTable;
  protected $baseTableAlias;
  protected $prefix;

  public function getPrefix() {
    return $this->prefix;
  }

  public function setPrefix($prefix) {
    $this->prefix = $prefix;
  }

  public function prefix($table) {
    if (substr($table, 0, 1) == "(") return $table;
    return $this->prefix.$table;
  }

  public function getTable($alias = false) {
    if (false === $alias) $alias = $this->baseTableAlias;
    return $this->tables[$alias];
  }

  public function hasTable($alias = false) {
    if (false === $alias) $alias = $this->baseTableAlias;
    return isset($this->tables[$alias]);
  }

  public function getAlias($table = false) {
    if (false === $table) return $this->baseTableAlias;
    foreach ($this->tables as $alias => $entry) {
      if ($entry->getName() == $table) return $alias;
    }
    return false;
  }

  public function setTable($table, $alias = false) {
    $table = $this->addTable($table, $alias);
    $this->baseTableAlias = $table->getAlias();
    $this->baseTable = $table->getName();
    return $table;
  }

  public function addTable($table, $alias = false) {
    $table = new Table($this, $table, $alias);
    $alias = $table->getAlias();
    $this->tables[$alias] = $table;
    return $table;
  }

  public function getTables() {
    return $this->tables;
  }
}
