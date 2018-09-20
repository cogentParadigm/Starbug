<?php
namespace Starbug\Db\Query\Traits;

use Starbug\Db\Query\Condition;

trait Conditions {
  protected $condition = null;
  protected $havingCondition = null;

  public function getCondition() {
    if (is_null($this->condition)) {
      $this->condition = $this->createCondition();
    }
    return $this->condition;
  }

  public function getHavingCondition() {
    if (is_null($this->havingCondition)) {
      $this->havingCondition = $this->createCondition();
    }
    return $this->havingCondition;
  }

  public function addCondition($field, $value = "", $operator = "=", $ops = []) {
    $this->getCondition()->condition($field, $value, $operator, $ops);
  }

  public function addWhere($condition, $ops = []) {
    $this->getCondition()->where($condition, $ops);
  }

  public function addHavingCondition($field, $value = "", $operator = "=", $ops = []) {
    $this->getHavingCondition()->condition($field, $value, $operator, $ops);
  }

  public function addHavingWhere($condition, $ops = []) {
    $this->getHavingCondition()->where($condition, $ops);
  }

  public function createCondition() {
    return new Condition();
  }

  public function createOrCondition() {
    return new Condition("OR");
  }
}
