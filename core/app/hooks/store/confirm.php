<?php
class hook_store_confirm extends QueryHook {
	function validate(&$query, $key, $value, $column, $argument) {
		if ((isset($query->fields[$argument])) && ($value != $query->fields[$argument]))  error("Your $column"." fields do not match", $column);
		$query->exclude($argument);
		return $value;
	}
}
?>
