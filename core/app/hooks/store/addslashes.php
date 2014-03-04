<?php
class hook_store_addslashes {
	function validate(&$query, $key, $value, $column, $argument) {
		return addslashes($value);
	}
}
?>
