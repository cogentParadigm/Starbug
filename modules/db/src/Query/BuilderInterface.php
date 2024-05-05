<?php
namespace Starbug\Db\Query;

use Starbug\Db\DatabaseInterface;
use IteratorAggregate;

interface BuilderInterface extends IteratorAggregate {
  /**
   * Specify fields for selection.
   *
   * @param string $field the name of the field
   */
  public function select($field, $prefix = "");
  /**
   * Add a collection or table to be queried.
   *
   * @param string $collection the name of the table or collection
   */
  public function from($tables);
  /**
   * Join a table to be queried.
   *
   * @param string $table the name of the table or collection
   * @param string $join the join type
   */
  public function join($table, $type = "");
  /**
   * Join a collection or table to be queried using an INNER join
   *
   * @param string $collection the name of the table or collection
   */
  public function innerJoin($table);
  /**
   * Join a collection or table to be queried using a LEFT join
   *
   * @param string $collection the name of the table or collection
   */
  public function leftJoin($table);
  /**
   * Join a collection or table to be queried using a RIGHT join.
   *
   * @param string $collection the name of the table or collection
   */
  public function rightJoin($table);
  /**
   * Specify the on clause for a join.
   *
   * @param string $expr the ON expression (not including 'ON ')
   * @param string $collection the name of the table or collection
   */
  public function on($condition, $alias = "");
  /**
   * Add a condition. you will probably want to use a more specific where or having function.
   *
   * @param ConditionInterface $conditions the conditions to add to
   * @param string $field the field or expression(s)
   * @param string $value the value to compare against
   * @param string $operator the operator (eg. '=', '<', '>')
   * @param star $options pass any of the following options
   *                   - op: the operator (eg. '=', '<', '>')
   *                   - con: the logical connective (eg. '&&', '||')
   */
  public function addCondition($conditions, $field, $value = "", $operator = "=", $options = []);
  /**
   * Add a conditional expression.
   *
   * @param ConditionInterface $conditions the conditions to add to
   * @param string|array|ConditionInterface|BuilderInterface $condition the where expression(s)
   * @param array $options (optional) pass any of the following options
   *                   - op: the operator (eg. '=', '<', '>')
   *                   - con: the logical connective (eg. '&&', '||')
   */
  public function addWhere($conditions, $condition, $options = []);
  public function condition($field, $value = "", $operator = "=", $options = []);
  public function conditions($fields, $operator = "=", $options = []);
  public function where($condition, $options = []);
  public function havingCondition($field, $value = "", $operator = "=", $options = []);
  public function havingWhere($condition, $options = []);
  public function createCondition();
  public function createOrCondition();
  /**
   * Add a parameter.
   *
   * @param string $name the parameter name
   * @param mixed $value the parameter value
   */
  public function bind($name, $value = null);
  /**
   * Add a field or fields to group by.
   *
   * @param string $column the column or group by statement
   */
  public function group($column);
  public function set($field, $value = null);
  public function exclude($column);
  /**
   * Add a field or fields to sort by.
   *
   * @param string $column the column or ORDER BY statement
   * @param int $direction (optional) sorting direction (-1 or 1)
   */
  public function sort($column, $direction = 0);
  /**
   * Add a limit.
   *
   * @param int|string $limit the limit or limit statement
   */
  public function limit($limit);
  /**
   * Set the number of records to skip.
   *
   * @param int $skip the number of records to skip
   */
  public function skip($skip);
  public function mode($mode);
  public function forUpdate($forUpdate = true);
  public function reset();
  public function getQuery();
  public function getDatabase(): DatabaseInterface;
}
