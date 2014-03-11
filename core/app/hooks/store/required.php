<?php
class hook_store_required {
	function empty_before_insert(&$query, $column, $argument) {
		if ($argument == "insert") error("This field is required.", $column);
	}
	function empty_before_update(&$query, $column, $argument) {
		if ($argument == "update") error("This field is required.", $column);
	}
	function empty_validate(&$query, $column, $argument) {
		if ($argument == "always") error("This field is required.", $column);
	}
	function validate(&$query, $key, $value, $column, $argument) {
		if ($value === "") error("This field is required", $column);
		return $value;
	}
}
?>
