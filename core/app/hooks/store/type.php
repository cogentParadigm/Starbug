<?php
class hook_store_type {
	function validate(&$query, $key, $value, $column, $argument) {
		if (sb()->has($argument)) $query->exclude($key);
		return $value;
	}
}
?>
