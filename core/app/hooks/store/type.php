<?php
class hook_store_type {
	function validate(&$query, $key, $value, $column, $argument) {;
		if (sb()->db->has($argument) || $argument == "category") $query->exclude($key);
		return $value;
	}
}
?>
