<?php
class hook_store_default extends QueryHook {
	function empty_before_insert(&$query, $column, $argument) {
		$query->set($column, $argument);
	}
	function validate(&$query, $key, $value, $column, $argument) {
		return ($value === "") ? $argument : $value;
	}
}
?>
