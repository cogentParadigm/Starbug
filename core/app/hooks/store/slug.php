<?php
//converts a string into a url slug and stores that in another field
class hook_store_slug {
	function validate(&$query, $key, $value, $column, $argument) {
		if (!isset($query->fields[$argument])) $query->set($argument, strtolower(str_replace(" ", "-", normalize($value))));
		else if (empty($query->fields[$argument])) $query->fields[$argument] = strtolower(str_replace(" ", "-", normalize($value)));
		return $value;
	}
}
?>
