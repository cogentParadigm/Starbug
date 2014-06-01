<?php
class hook_store_references {
	var $replace = false;
	function validate(&$query, $key, $value, $column, $argument) {
		if (empty($value)) {
			$this->replace = true;
			$value = "";
		}
		return $value;
	}
	function store(&$query, $key, $value, $column, $argument) {
		$model = reset(explode(" ", $argument));
		return ($value === "") ? sb($model)->insert_id : $value;
	}
}
?>
