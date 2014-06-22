<?php
class hook_store_type {
	function empty_validate(&$query, $column, $argument) {
		if (sb()->db->has($argument) || $argument == "category") $query->exclude($column);
	}
	function validate(&$query, $key, $value, $column, $argument) {;
		if (sb()->db->has($argument) || $argument == "category") $query->exclude($key);
		return $value;
	}
}
?>
