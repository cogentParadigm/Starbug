<?php
namespace Starbug\Db\Query;

use Countable;

interface ConditionInterface extends Countable {
  public function condition($field, $value = "", $operator = "=", $ops = []);
  public function orCondition($field, $value = "", $operator = "=", $ops = []);
  public function andCondition($field, $value = "", $operator = "=", $ops = []);
  public function where($condition, $ops = []);
  public function orWhere($condition, $ops = []);
  public function andWhere($condition, $ops = []);
  public function removeCondition($properties);
  public function getConditions();
  public function getConjunction();
  public function setConditions(array $conditions);
}
