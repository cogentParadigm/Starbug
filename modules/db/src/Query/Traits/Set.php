<?php
namespace Starbug\Db\Query\Traits;

trait Set {
	protected $values = [];

	public function setValue($field, $value) {
		$this->values[$field] = $value;
	}

	public function getValue($field) {
		return $this->values[$field];
	}

	public function getValues() {
		return $this->values;
	}

}
