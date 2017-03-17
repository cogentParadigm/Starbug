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
			$this->conditions[] = $field;
		} else {
			$condition = array_merge(["field" => $field, "value" => $value, "operator" => $operator], $ops);
			$this->conditions[] = $condition;
		}
		return $this;
	}

	public function where($condition, $ops = []) {
		$condition = array_merge(["condition" => $condition], $ops);
		$this->conditions[] = $condition;
		return $this;
	}

	public function removeCondition($properties) {
		foreach ($this->conditions as $idx => $condition) {
			$remove = true;
			if ($condition instanceof ConditionInterface) {
				$condition->removeCondition($properties);
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

	public function count() {
		return count($this->conditions);
	}
}
