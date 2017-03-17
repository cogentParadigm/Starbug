<?php
namespace Starbug\Db\Query;

class Builder implements BuilderInterface {

	use Traits\Hooks;
	use Traits\Parsing;

	protected $query;
	protected $lastTableAlias;

	public function __construct(QueryInterface $query) {
		$this->query = $query;
	}

	public function get($alias = false) {
		return $this->query->getTable($alias);
	}

	public function has($alias = false) {
		return $this->query->hasTable($alias);
	}

	//SELECT, DELETE

	/**
	 * specify fields for selection
	 * @param string $field the name of the field
	 */
	function select($field, $prefix = "") {
		if (is_array($field)) {
			foreach ($field as $f) $this->select($f, $prefix);
		} else {
			if (!empty($prefix)) $field = $prefix.".".$field;
			$field = $this->parseName($field);
			$this->query->addSelection($field['name'], $field["alias"]);
		}
		return $this;
	}

	//FROM

	/**
	 * set base table
	 * @param string $collection the name of the table or collection
	 */
	function from($table) {
		$table = $this->parseName($table);
		$this->lastTableAlias = $this->query->setTable($table["name"], $table["alias"]);
		return $this;
	}

	//JOIN

	/**
	 * join a table to be queried
	 * @param string $table the name of the table or collection
	 * @param string $join the join type
	 */
	function join($table, $type = "") {
		$table = $this->parseName($table);
		$join = $this->query->addJoin($table["name"], $table["alias"]);
		$this->lastTableAlias = $join->getAlias();
		if (!empty($type)) $join->setType($type);
		if (!empty($table['on'])) $join->where($table['on']);
		return $this;
	}

	/**
	 * join a collection or table to be queried using an INNER join
	 * @param string $collection the name of the table or collection
	 */
	function innerJoin($table) {
		return $this->join($table, JoinType::INNER);
	}

	/**
	 * join a collection or table to be queried using a LEFT join
	 * @param string $collection the name of the table or collection
	 */
	function leftJoin($table) {
		return $this->join($table, JoinType::LEFT);
	}

	/**
	 * join a collection or table to be queried using a RIGHT join
	 * @param string $collection the name of the table or collection
	 */
	function rightJoin($table) {
		return $this->join($table, JoinType::RIGHT);
	}

	//ON

	/**
	 * specify the on clause for a join
	 * @param string $expr the ON expression (not including 'ON ')
	 * @param string $collection the name of the table or collection
	 */
	function on($condition, $alias = "") {
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

	//EXPAND

	/**
	 * Expand a reference to another table.
	 * examples: pages.owner, pages.comments, pages.comments.owner
	 */
	public function expand($column) {
		//split the column into parts
		$parts = explode(".", $column);
		//the first token is either a table alias or the name of a column that references another table
		//if it's a column, then we'll assume it's a column of our base table
		$alias = $this->query->getAlias();
		//if it's a collection, we'll use it
		if ($this->query->hasTable($parts[0])) {
			$alias = array_shift($parts);
		}
		//now we start a loop to process the remaining tokens
		while (!empty($parts)) {
			//shift off the first token
			$token = array_shift($parts);
			//parse and join this reference
			$parsed = $this->parser->parseName($token);
			$table = $this->query->getTable($alias)->getName();
			$schema = $this->schema->getColumn($table, $parsed["name"]);
			if ($schema["entity"] !== $table) {
				$parentAlias = $alias."_".$schema["entity"];
				if (!$this->has($parentAlias)) {
					$root = $this->schema->getEntityRoot($table);
					$join = $this->query->addJoin($schema["entity"], $parentAlias);
					if ($schema["entity"] == $root) $join->on($parentAlias.".id=".$alias.".".$root."_id");
					else $join->on($parentAlias.".".$root."_id=".$alias.".".$root."_id");
				}
				$table = $schema["entity"];
				$alias = $parentAlias;
			}
			$nextAlias = (empty($parts)) ? $parsed["alias"] : $alias."_".$parsed["name"];
			if (!$this->has($nextAlias)) {
				if (!empty($schema["references"])) {
					$ref = explode(" ", $schema["references"]);
					$this->query->addJoinOne($alias.".".$parsed["name"], $ref[0], $nextAlias);
				} elseif ($this->schema->hasTable($schema["type"])) {
					if ($schema["table"] == $schema["type"]) {
						$this->addJoinMany($alias, $schema["type"], $nextAlias);
					} else {
						$this->addJoinMany($alias, $schema["table"], $nextAlias."_lookup");
						$this->addJoinOne($nextAlias."_lookup.".$parsed["name"]."_id", $schema["type"], $nextAlias);
					}
				}
			}
			$alias = $nextAlias;
		}
		return $this;
	}

	//CONDITIONS

	/**
	 * add a condition. you will probably want to use a more specific where or having function
	 * @param ConditionInterface $conditions the conditions to add to
	 * @param string $field the field or expression(s)
	 * @param string $value the value to compare against
	 * @param string $operator the operator (eg. '=', '<', '>')
	 * @param star $options pass any of the following options
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	public function addCondition($conditions, $field, $value = "", $operator = "=", $options = []) {
		if ($field instanceof BuilderInterface) {
			foreach ($field->getHistory() as $set => $operations) {
				foreach ($operations as $operation) {
					if ($operation['operation'] == "condition") $this->addCondition($conditions, $operation['field'], $operation['value'], $operation['operator'], $operation['options']);
					else if ($operation['operation'] == "where") $this->addWhere($conditions, $operation['condition'], $operation['options']);
				}
			}
			return $this;
		} else if (is_array($field)) {
			foreach ($field as $k => $v) $this->addCondition($conditions, $k, $v, $operator, $options);
			return $this;
		} else {
			$conditions->condition($field, $value, $operator, $options);
			return $this;
		}
	}

	/**
	 * add a conditional expression
	 * @param ConditionInterface $conditions the conditions to add to
	 * @param string|array|ConditionInterface|BuilderInterface $condition the where expression(s)
	 * @param array $options (optional) pass any of the following options
	 * 									- op: the operator (eg. '=', '<', '>')
	 * 									- con: the logical connective (eg. '&&', '||')
	 */
	function addWhere($conditions, $condition, $options = []) {
		if ($condition instanceof BuilderInterface) {
			foreach ($condition->getHistory() as $set => $operations) {
				foreach ($operations as $operation) {
					if ($operation['operation'] == "condition") $this->addCondition($conditions, $operation['field'], $operation['value'], $operation['operator'], $operation['options']);
					else if ($operation['operation'] == "where") $this->addWhere($conditions, $operation['condition'], $operation['options']);
				}
			}
			return $this;
		} else if (is_array($condition)) {
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

	public function where($condition, $options = []) {
		return $this->addWhere($this->query->getCondition(), $condition, $options);
	}

	public function havingCondition($field, $value = "", $operator = "=", $options = []) {
		return $this->addCondition($this->query->getHavingCondition(), $field, $value, $operator, $options);
	}

	public function havingWhere($condition, $options = []) {
		return $this->addWhere($this->query->getHavingCondition(), $condition, $options);
	}

	public function createCondition() {
		return $this->query->createCondition();
	}

	public function createOrCondition() {
		return $this->query->createOrCondition();
	}

	/**
	 * add a parameter
	 * @param string $name the parameter name
	 * @param mixed $value the parameter value
	 */
	function bind($name, $value = null) {
		$this->query->setParameter($name, $value);
		return $this;
	}

	/**
	 * add a field or fields to group by
	 * @param string $column the column or group by statement
	 */
	function group($column) {
		$this->query->addGroup($column);
		return $this;
	}

	function set($field, $value = null) {
		if (is_array($field)) {
			foreach ($field as $key => $value) $this->set($key, $value);
		} else {
			$this->query->setValue($field, $value);
		}
		return $this;
	}

	/**
	 * add a field or fields to sort by
	 * @param string $column the column or ORDER BY statement
	 * @param int $direction (optional) sorting direction (-1 or 1)
	 */
	function sort($column, $direction = 0) {
		$this->query->addSort($column, $direction);
		return $this;
	}

	/**
	 * add a limit
	 * @param int|string $limit the limit or limit statement
	 */
	function limit($limit) {
		$this->query->setLimit($limit);
		return $this;
	}

	/**
	 * set the number of records to skip
	 * @param int $skip the number of records to skip
	 */
	function skip($skip) {
		$this->query->setSkip($skip);
		return $this;
	}

	public function mode($mode) {
		$this->query->setMode($mode);
		return $this;
	}

	public function setQuery(QueryInterface $query) {
		$this->query = $query;
		return $this;
	}
	public function getQuery() {
		return $this->query;
	}
	protected function createQuery() {
		return $this->query->createSubquery();
	}
}
