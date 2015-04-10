<?php
class hook_store_required extends QueryHook {
	function empty_before_insert(&$query, $column, $argument) {
		if ($argument == "insert") error("This field is required.", $column);
	}
	function empty_before_update(&$query, $column, $argument) {
		if ($argument == "update") error("This field is required.", $column);
	}
	function empty_validate(&$query, $column, $argument) {
		if ($argument == "always") error("This field is required.", $column);
	}
	function store(&$query, $key, $value, $column, $argument) {
		if ($value === "" && empty($query->exclusions[$key])) error("This field is required", $column);
		return $value;
	}
}
?>
