<?php
class hook_store_owner {
	function empty_insert(&$query, $column, $argument) {
		$query->set($column, (logged_in() ? sb()->user['id'] : 1));
	}
	function validate(&$query, $key, $value, $column, $argument) {
		return logged_in() ? sb()->user['id'] : 1;
	}
}
?>
