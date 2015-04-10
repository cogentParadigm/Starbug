<?php
class hook_store_length extends QueryHook {
	function validate(&$query, $key, $value, $column, $argument) {
		$length = explode("-", $argument);
		if (!next($length)) $length = array(0, $length[0]);
		$value_length = strlen($value);
		if (!($value_length >= $length[0] && $value_length <= $length[1])) error("This field must be between $length[0] and $length[1] characters long.", $column);
		return $value;
	}
}
?>
