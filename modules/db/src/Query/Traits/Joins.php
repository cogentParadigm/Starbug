<?php
namespace Starbug\Db\Query\Traits;

use Starbug\Db\Query\JoinType;
use Starbug\Db\Query\RelationshipType;

trait Joins {

  public function addJoin($table, $alias = false) {
    return $this->addTable($table, $alias);
  }

  public function addInnerJoin($table, $alias = false) {
    return $this->addJoin($table, $alias)->setType(JoinType::INNER);
  }

  public function addLeftJoin($table, $alias = false) {
    return $this->addJoin($table, $alias)->setType(JoinType::LEFT);
  }

  public function addRightJoin($table, $alias = false) {
    return $this->addJoin($table, $alias)->setType(JoinType::RIGHT);
  }

  public function getJoin($alias) {
    return $this->getTable($alias);
  }

  public function addJoinOne($column, $target, $alias = false) {
    return $this->addLeftJoin($target, $alias)->to(RelationshipType::ONE)->via($column);
  }

  public function addJoinMany($base, $target, $alias = false) {
    return $this->addLeftJoin($target, $alias)->to(RelationshipType::MANY)->via($base);
  }
}
