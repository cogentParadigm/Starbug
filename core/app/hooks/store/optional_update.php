<?php
class hook_store_optional_update {
	function before_update(&$query, $key, $value, $column, $argument) {
		if (empty($value)) $query->exclude($key);
		return $value;
	}
}
?>
