<?php
namespace Starbug\Db\Query\Traits;

trait Set {
	protected $values = [];
	protected $unvalidatedValues = [];

	public function setValue($field, $value) {
		$this->values[$field] = $value;
	}

	public function getValue($field) {
		return $this->values[$field];
	}

	public function getValues() {
		return $this->values;
	}

	public function setValues(array $values) {
		$this->values = $values;
	}

	public function setValidatedValues($values) {
		if (empty($this->unvalidatedValues)) $this->unvalidatedValues = $this->values;
		$this->values = $values;
	}

	public function getUnvalidatedValue($field) {
		if (isset($this->unvalidatedValues[$field])) return $this->unvalidatedValues[$field];
		return $this->getValue($field);
	}

}
