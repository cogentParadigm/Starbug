<?php
namespace Starbug\Db\Query;

use Starbug\Core\DatabaseInterface;

class Compiler implements CompilerInterface {
	protected $parameterCount = [];
	protected $prefix;
	protected $hooks = [];

	public function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}

	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}

	public function getPrefix() {
		return $this->db->prefix("");
	}

	public function build(QueryInterface $query) {
		$this->parameterCount = [];

		$this->invokeHooks("beforeCompileQuery", [$query, $this]);

		$components = $this->buildComponentClauses($query);

		$sql = [];
		foreach ($components as $key => $clause) {
			$sql[$key] = $key." ".$clause;
		}
		$sqlQuery = implode(' ', $sql);
		$sqlCountQuery = "";

		unset($sql['LIMIT']);
		unset($sql['ORDER BY']);
		if (!empty($components['HAVING'])) {
			$sqlCountQuery = "SELECT COUNT(*) as count FROM (".implode(' ', $sql).") as c";
		} else if (!empty($components['GROUP BY'])) {
			$sql['SELECT'] = "SELECT COUNT(DISTINCT ".$components['GROUP BY'].") as count";
			unset($sql['GROUP BY']);
			$sqlCountQuery = implode(' ', $sql);
		} else {
			if (!$query->isSelect()) $components['SELECT'] = "*";
			$sql['SELECT'] = "SELECT COUNT(".((false !== strpos(strtolower($components['SELECT']), 'distinct')) ? $components['SELECT'] : "*").") as count";
			$sqlCountQuery = implode(' ', $sql);
		}

		return new CompiledQuery($sqlQuery, $sqlCountQuery);
	}

	protected function buildSubquery(QueryInterface $query, QueryInterface $parent) {
		$result = $this->build($query);
		$parameters = $query->getParameters();
		foreach ($parameters as $name => $value) {
			$parent->setParameter($name, $value);
		}
		return $result;
	}

	protected function buildQuery($query) {
		$components = $this->buildComponentClauses($query);
		$sql = [];
		foreach ($components as $key => $clause) {
			$sql[$key] = $key." ".$clause;
		}
		return implode(' ', $sql);
	}

	protected function buildComponentClauses($query) {
		$components = array(
			'SELECT' => '', //query
			'DELETE' => '', //delete
			'INSERT INTO' => '', //insert
			'UPDATE' => '', //update
			'TRUNCATE TABLE' => '', //truncate
			'FROM' => '', //query, delete
			'SET' => '', //insert, update
			'WHERE' => '', //query, delete, update
			'GROUP BY' => '', //query
			'HAVING' => '', //query
			'ORDER BY' => '', //query, delete (single table), update (single table)
			'LIMIT' => '' //query, delete (single table), update (single table)
		);

		//select, delete, or set
		if ($query->isSelect()) $components['SELECT'] = $this->buildSelect($query);
		else if ($query->isDelete()) $components['DELETE'] = $this->buildSelect($query);
		else if ($query->isInsert() || $query->isUpdate()) $components['SET'] = $this->buildSet($query);

		//where
		if ($query->isSelect() || $query->isUpdate() || $query->isDelete()) $components['WHERE'] = $this->buildCondition($query, $query->getCondition());

		//group
		if ($query->isSelect()) $components['GROUP BY'] = $this->buildGroup($query);

		//having
		if ($query->isSelect()) $components['HAVING'] = $this->buildCondition($query, $query->getHavingCondition());

		//order
		if ($query->isSelect() || $query->isUpdate() || $query->isDelete()) $components['ORDER BY'] = $this->buildSort($query);

		//limit
		if ($query->isSelect() || $query->isUpdate() || $query->isDelete()) $components['LIMIT'] = $this->buildLimit($query);

		//from
		if ($query->isSelect() || $query->isDelete()) $components['FROM'] = $this->buildFrom($query);
		else if ($query->isInsert()) $components['INSERT INTO'] = $this->buildFrom($query);
		else if ($query->isUpdate()) $components['UPDATE'] = $this->buildFrom($query);
		else if ($query->isTruncate()) $components['TRUNCATE TABLE'] = $this->buildFrom($query);

		foreach ($components as $key => $clause) if (empty($clause)) unset($components[$key]);

		return $components;
	}

	protected function buildSelect($query) {
		$component = $query->getSelection();
		$select = array();
		if (empty($component)) $select[] = "`".$query->getAlias()."`.*";
		else {
			foreach ($component as $alias => $field) {
				if ($field instanceof QueryInterface) {
					$field = "(".$this->buildQuery($field).")";
				}
				if ($alias != $field) $field .= " AS ".$alias;
				$select[] = $field;
			}
		}
		return implode(", ", $select);
	}

	function buildFrom($query) {
		$baseTable = $query->getTable();
		$baseTableName = $baseTable->getName();
		$baseTableAlias = $baseTable->getAlias();
		$tables = $query->getTables();
		$from = "`".$this->prefix($baseTableName)."`";
		if (!$query->isInsert() && !$query->isTruncate()) $from .= " AS `".$baseTableAlias."`";
		foreach ($tables as $alias => $table) {
			if ($alias == $baseTableAlias) continue;
			$tableSegment = ("(" === substr($table->getName(), 0, 1)) ? $table->getName() : "`".$this->prefix($table->getName())."`";
			$joinType = $table->getJoinType();
			$joinType = $joinType ? " ".$joinType : "";
			$segment = $joinType." JOIN ".$tableSegment." AS `".$alias."`";
			if (count($table) > 0) {
				$segment .= " ON ".$this->buildCondition($query, $table);
			}
			$from .= $segment;
		}
		return $from;
	}

	function buildSet($query) {
		$set = array();
		$values = $query->getValues();
		foreach ($values as $name => $value) {
			if (!$query->isExcluded($name)) {
				if ($value == "NULL") $value = null;
				$idx = $this->incrementParameterIndex("set");
				$set[] = "`".str_replace(".", "`.`", str_replace('`', '', $name))."` = :set".$idx;
				$query->setParameter("set".$idx, $value);
			}
		}
		return implode(", ", $set);
	}

	function buildCondition($query, $set) {
		$conjunction = $set->getConjunction();
		$set = $set->getConditions();
		if (empty($set)) return "";
		foreach ($set as $idx => $condition) {
			if ($condition instanceof ConditionInterface) {
				$set[$idx] = "(".$this->buildCondition($query, $condition).")";
			} else if (!empty($condition["condition"])) {
				$set[$idx] = $condition["condition"];
			} else {
				$conditions = "";
				if ($condition["field"] instanceof BuilderInterface) {
					$condition["field"] = $condition["field"]->getQuery();
				}
				if ($condition["field"] instanceof QueryInterface) {
					$condition["field"] = "(".$this->buildSubquery($condition["field"], $query)->getSql().")";
					$condition["invert"] = true;
				}
				if (!empty($condition['ornull']) && $condition['operator'] === "!=") $conditions .= "(".$condition['field']." is NULL || ";
				if (empty($condition['invert'])) $conditions .= $condition['field'];
				if (!is_null($condition['value'])) {
					if (is_array($condition['value'])) {
						$condition['operator'] = str_replace(array('!', '='), array("NOT ", "IN"), $condition['operator']);
						if ($condition['invert']) {
							$conditions .= "(";
							foreach ($condition['value'] as $vdx => $condition_value) {
								$index = $this->incrementParameterIndex();
								if ($vdx > 0) $conditions .= " || ";
								$conditions .= ":default".$index." ".$condition['operator']." ".$condition['field'];
								$query->setParameter("default".$index, $condition_value);
							}
							$conditions .= ")";
						} else {
							$conditions .= ' '.$condition['operator'].' (';
							foreach ($condition['value'] as $vdx => $condition_value) {
								$index = $this->incrementParameterIndex();
								if ($vdx > 0) $conditions .= ", ";
								$conditions .= ":default".$index;
								$query->setParameter("default".$index, $condition_value);
							}
							$conditions .= ')';
						}
					} else if ($condition['value'] === "NULL") {
						$condition['operator'] = str_replace(array('!=', '='), array("IS NOT ", "IS"), $condition['operator']);
						$conditions .= ' '.$condition['operator'].' NULL';
					} elseif ($condition["value"] instanceof BuilderInterface) {
						$condition['operator'] = str_replace(array('!', '='), array("NOT ", "IN"), $condition['operator']);
						$condition["value"] = "(".$this->buildSubquery($condition["value"]->getQuery(), $query)->getSql().")";
						$conditions .= ' '.$condition['operator'].' '.$condition["value"];
					} elseif ($condition["value"] instanceof QueryInterface) {
						$condition['operator'] = str_replace(array('!', '='), array("NOT ", "IN"), $condition['operator']);
						$condition["value"] = "(".$this->buildSubquery($condition["value"], $query)->getSql().")";
						$conditions .= ' '.$condition['operator'].' '.$condition["value"];
					} else {
						$unary = false;
						$index = $this->incrementParameterIndex();
						if (!empty($condition['invert'])) {
							$condition['operator'] = str_replace(array('!', '='), array("NOT ", "IN"), $condition['operator']);
							if (in_array($condition["operator"], ["EXISTS", "NOT EXISTS"])) {
								$unary = true;
								$conditions .= $condition['operator']." ".$condition['field'];
							} else {
								$conditions .= ":default".$index." ".$condition['operator']." ".$condition['field'];
							}
						} else $conditions .= ' '.$condition['operator'].' :default'.$index;
						if (!$unary) $query->setParameter("default".$index, $condition['value']);
					}
				}
				if (!empty($condition['ornull']) && $condition['operator'] === "!=") $conditions .= ")";
				$set[$idx] = $conditions;
			}
		}
		return implode(" ".$conjunction." ", $set);
	}

	function buildGroup($query) {
		return implode(', ', array_keys($query->getGroup()));
	}

	function buildSort($query) {
		$sort = array();
		foreach ($query->getSort() as $column => $direction) {
			if ($direction === -1) $column .= " DESC";
			else if ($direction === 1) $column .= " ASC";
			$sort[] = $column;
		}
		return implode(', ', $sort);
	}

	function buildLimit($query) {
		$limit = array();
		if (!empty($query->getSkip())) $limit[] = $query->getSkip();
		if (!empty($query->getLimit())) $limit[] = $query->getLimit();
		return implode(', ', $limit);
	}

	/**
	 * internal function for incrementing a count to generate a unique placholder string for parameters
	 * @return int the next number
	 */
	protected function incrementParameterIndex($set = "default") {
		if (!isset($this->parameterCount[$set])) $this->parameterCount[$set] = 0;
		return $this->parameterCount[$set]++;
	}

	protected function prefix($table) {
		return $this->db->prefix($table);
	}

	public function addHook(CompilerHookInterface $hook) {
		$this->hooks[] = $hook;
		return $this;
	}

	public function addHooks($hooks) {
		foreach ($hooks as $hook) {
			$this->addHook($hook);
		}
		return $this;
	}

	protected function invokeHooks($method, $args) {
		foreach ($this->hooks as $hook) {
			call_user_func_array([$hook, $method], $args);
		}
	}
}
