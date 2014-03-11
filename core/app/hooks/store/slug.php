<?php
//converts a string into a url slug and stores that in another field
class hook_store_addslashes {
	function validate(&$query, $key, $value, $column, $argument) {
		$query->set($argument, strtolower(str_replace(" ", "-", normalize($value))));
		return $value;
	}
}
?>
