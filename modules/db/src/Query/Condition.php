<?php
namespace Starbug\Db\Query;

class Condition implements ConditionInterface {
  protected $conjunction;
  protected $conditions = [];

  public function __construct($conjunction = "AND") {
    $this->conjunction = $conjunction;
  }

  public function condition($field, $value = "", $operator = "=", $ops = []) {
    if (is_null($value)) $value = "";
    if ($field instanceof ConditionInterface) {
      $condition = array_merge(["condition" => $field], $ops);
      $this->conditions[] = $condition;
    } else {
      $condition = array_merge(["field" => $field, "value" => $value, "operator" => $operator], $ops);
      $this->conditions[] = $condition;
    }
    return $this;
  }

  public function orCondition($field, $value = "", $operator = "=", $ops = []) {
    return $this->condition($field, $value, $operator, ["con" => "OR"] + $ops);
  }

  public function andCondition($field, $value = "", $operator = "=", $ops = []) {
    return $this->condition($field, $value, $operator, ["con" => "AND"] + $ops);
  }

  public function where($condition, $ops = []) {
    $condition = array_merge(["condition" => $condition], $ops);
    $this->conditions[] = $condition;
    return $this;
  }

  public function orWhere($condition, $ops = []) {
    return $this->where($condition, ["con" => "OR"] + $ops);
  }

  public function andWhere($condition, $ops = []) {
    return $this->where($condition, ["con" => "AND"] + $ops);
  }

  public function removeCondition($properties) {
    foreach ($this->conditions as $idx => $condition) {
      $remove = true;
      if (!empty($condition["condition"]) && $condition["condition"] instanceof ConditionInterface) {
        $condition["condition"]->removeCondition($properties);
        if (!empty($condition)) $remove = false;
      } else {
        foreach ($properties as $key => $value) {
          if (empty($condition[$key]) || $condition[$key] != $value) $remove = false;
        }
      }
      if ($remove) unset($this->conditions[$idx]);
    }
    return $this;
  }

  public function getConditions() {
    return $this->conditions;
  }

  public function getConjunction() {
    return $this->conjunction;
  }

  public function setConditions(array $conditions) {
    $this->conditions = $conditions;
  }

  public function count() {
    return count($this->conditions);
  }
}
