<?php
class hook_store_owner extends QueryHook {
	function empty_before_insert(&$query, $column, $argument) {
		$query->set($column, (logged_in() ? sb()->user['id'] : "NULL"));
	}
	function validate(&$query, $key, $value, $column, $argument) {
		return logged_in() ? sb()->user['id'] : "NULL";
	}
}
?>
