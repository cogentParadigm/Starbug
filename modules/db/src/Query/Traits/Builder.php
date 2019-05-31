<?php
namespace Starbug\Db\Query\Traits;

use Starbug\Db\Query\Query;
use Starbug\Db\Query\JoinType;
use Starbug\Db\Query\BuilderInterface;
use Closure;

trait Builder {
  protected $query;
  protected $lastTableAlias;

  public function get($alias = false) {
    return $this->query->getTable($alias);
  }

  public function has($alias = false) {
    return $this->query->hasTable($alias);
  }

  // SELECT, DELETE

  /**
   * Specify fields for selection.
   *
   * @param string $field the name of the field
   */
  public function select($field, $prefix = "") {
    if (is_array($field)) {
      foreach ($field as $f) $this->select($f, $prefix);
    } else {
      if (!empty($prefix)) $field = $prefix.".".$field;
      $field = $this->parseName($field);
      $this->query->addSelection($field['name'], $field["alias"]);
    }
    return $this;
  }

  // FROM

  /**
   * Set base table.
   *
   * @param string $collection the name of the table or collection
   */
  public function from($table) {
    $table = $this->parseName($table);
    $this->lastTableAlias = $this->query->setTable($table["name"], $table["alias"]);
    return $this;
  }

  // JOIN

  /**
   * Join a table to be queried.
   *
   * @param string $table the name of the table or collection
   * @param string $join the join type
   */
  public function join($table, $type = "") {
    $table = $this->parseName($table);
    $join = $this->query->addJoin($table["name"], $table["alias"]);
    $this->lastTableAlias = $join->getAlias();
    if (!empty($type)) $join->setType($type);
    if (!empty($table['on'])) $join->where($table['on']);
    return $this;
  }

  /**
   * Join a collection or table to be queried using an INNER join.
   *
   * @param string $collection the name of the table or collection
   */
  public function innerJoin($table) {
    return $this->join($table, JoinType::INNER);
  }

  /**
   * Join a collection or table to be queried using a LEFT join.
   *
   * @param string $collection the name of the table or collection
   */
  public function leftJoin($table) {
    return $this->join($table, JoinType::LEFT);
  }

  /**
   * Join a collection or table to be queried using a RIGHT join
   *
   * @param string $collection the name of the table or collection
   */
  public function rightJoin($table) {
    return $this->join($table, JoinType::RIGHT);
  }

  // ON

  /**
   * Specify the on clause for a join.
   *
   * @param string $expr the ON expression (not including 'ON ')
   * @param string $collection the name of the table or collection
   */
  public function on($condition, $alias = "") {
    if (empty($alias)) $alias = $this->lastTableAlias;
    $this->query->getJoin($alias)->where($condition);
    return $this;
  }

  public function joinOne($column, $target, $alias = false) {
    $this->query->addJoinOne($column, $target, $alias);
    return $this;
  }

  public function joinMany($base, $target, $alias = false) {
    $this->query->addJoinMany($base, $target, $alias);
    return $this;
  }

  // CONDITIONS

  /**
   * Add a condition. you will probably want to use a more specific where or having function.
   *
   * @param ConditionInterface $conditions the conditions to add to
   * @param string $field the field or expression(s)
   * @param string $value the value to compare against
   * @param string $operator the operator (eg. '=', '<', '>')
   * @param array $options pass any of the following options
   *              - op: the operator (eg. '=', '<', '>')
   *              - con: the logical connective (eg. '&&', '||')
   */
  public function addCondition($conditions, $field, $value = "", $operator = "=", $options = []) {
    if (is_array($field)) {
      foreach ($field as $k => $v) $this->addCondition($conditions, $k, $v, $operator, $options);
      return $this;
    } else {
      if (is_object($field) && $field instanceof Closure) {
        $field = call_user_func($field, $this);
      }
      if ($field instanceof BuilderInterface) {
        $field = $field->getQuery();
      }
      $conditions->condition($field, $value, $operator, $options);
      return $this;
    }
  }

  /**
   * Add a conditional expression.
   *
   * @param ConditionInterface $conditions the conditions to add to
   * @param string|array|ConditionInterface|BuilderInterface $condition the where expression(s)
   * @param array $options (optional) pass any of the following options
   *                  - op: the operator (eg. '=', '<', '>')
   *                  - con: the logical connective (eg. '&&', '||')
   */
  public function addWhere($conditions, $condition, $options = []) {
    if ($condition instanceof BuilderInterface) {
      foreach ($condition->getHistory() as $set => $operations) {
        foreach ($operations as $operation) {
          if ($operation['operation'] == "condition") $this->addCondition($conditions, $operation['field'], $operation['value'], $operation['operator'], $operation['options']);
          elseif ($operation['operation'] == "where") $this->addWhere($conditions, $operation['condition'], $operation['options']);
        }
      }
      return $this;
    } elseif (is_array($condition)) {
      foreach ($condition as $c) $this->addWhere($conditions, $c, $options);
      return $this;
    } else {
      $conditions->where($condition, $options);
      return $this;
    }
  }

  public function condition($field, $value = "", $operator = "=", $options = []) {
    return $this->addCondition($this->query->getCondition(), $field, $value, $operator, $options);
  }

  public function conditions($fields, $operator = "=", $options = []) {
    return $this->condition($fields, "", $operator, $options);
  }

  public function orCondition($field, $value = "", $operator = "=", $options = []) {
    return $this->condition($field, $value, $operator, ["con" => "OR"] + $options);
  }

  public function andCondition($field, $value = "", $operator = "=", $options = []) {
    return $this->condition($field, $value, $operator, ["con" => "AND"] + $options);
  }

  public function where($condition, $options = []) {
    return $this->addWhere($this->query->getCondition(), $condition, $options);
  }

  public function orWhere($condition, $options = []) {
    return $this->where($condition, ["con" => "OR"] + $options);
  }

  public function andWhere($condition, $options = []) {
    return $this->where($condition, ["con" => "AND"] + $options);
  }

  public function havingCondition($field, $value = "", $operator = "=", $options = []) {
    return $this->addCondition($this->query->getHavingCondition(), $field, $value, $operator, $options);
  }

  public function orHavingCondition($field, $value = "", $operator = "=", $options = []) {
    return $this->havingCondition($field, $value, $operator, ["con" => "OR"] + $options);
  }

  public function andHavingCondition($field, $value = "", $operator = "=", $options = []) {
    return $this->havingCondition($field, $value, $operator, ["con" => "AND"] + $options);
  }

  public function havingWhere($condition, $options = []) {
    return $this->addWhere($this->query->getHavingCondition(), $condition, $options);
  }

  public function orHavingWhere($condition, $options = []) {
    return $this->havingWhere($condition, ["con" => "OR"] + $options);
  }

  public function andHavingWhere($condition, $options = []) {
    return $this->havingWhere($condition, ["con" => "AND"] + $options);
  }

  public function createCondition() {
    return $this->query->createCondition();
  }

  public function createOrCondition() {
    return $this->query->createOrCondition();
  }

  /**
   * Add a parameter.
   *
   * @param string $name the parameter name
   * @param mixed $value the parameter value
   */
  public function bind($name, $value = null) {
    $this->query->setParameter($name, $value);
    return $this;
  }

  /**
   * Add a field or fields to group by.
   *
   * @param string $column the column or group by statement
   */
  public function group($column) {
    $this->query->addGroup($column);
    return $this;
  }

  public function set($field, $value = null) {
    if (is_array($field)) {
      foreach ($field as $key => $value) $this->set($key, $value);
    } else {
      $this->query->setValue($field, $value);
    }
    return $this;
  }

  public function exclude($column) {
    $this->query->addExclusion($column);
    return $this;
  }

  /**
   * Add a field or fields to sort by.
   *
   * @param string $column the column or ORDER BY statement
   * @param int $direction (optional) sorting direction (-1 or 1)
   */
  public function sort($column, $direction = 0) {
    $this->query->addSort($column, $direction);
    return $this;
  }

  /**
   * Add a limit.
   *
   * @param int|string $limit the limit or limit statement
   */
  public function limit($limit) {
    $this->query->setLimit($limit);
    return $this;
  }

  /**
   * Set the number of records to skip.
   *
   * @param int $skip the number of records to skip
   */
  public function skip($skip) {
    $this->query->setSkip($skip);
    return $this;
  }

  public function mode($mode) {
    $this->query->setMode($mode);
    return $this;
  }

  public function raw($raw = true) {
    $this->query->setRaw($raw);
    return $this;
  }

  public function forUpdate($forUpdate = true) {
    $this->query->setForUpdate($forUpdate);
    return $this;
  }

  public function reset() {
    $this->query = $this->createQuery();
    return $this;
  }

  public function getQuery() {
    return $this->query;
  }

  protected function createQuery() {
    return new Query($this->db->getPrefix());
  }
}
