<?php
namespace Starbug\Core;
class hook_store_time extends QueryHook {
	function empty_before_insert(&$query, $column, $argument) {
		$query->set($column, date("Y-m-d H:i:s"));
	}
	function empty_before_update(&$query, $column, $argument) {
		if ($argument == "update") $query->set($column, date("Y-m-d H:i:s"));
	}
	function before_insert(&$query, $key, $value, $column, $argument) {
		return date("Y-m-d H:i:s");
	}
	function before_update(&$query, $key, $value, $column, $argument) {
		if ($argument == "insert") $query->exclude($key);
		return date("Y-m-d H:i:s");
	}
}
?>
