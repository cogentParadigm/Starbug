<?php
class hook_store_password {
	function validate(&$query, $key, $value, $column, $argument) {
		return (empty($value) ? $value : Session::hash_password($value));
	}
}
?>
