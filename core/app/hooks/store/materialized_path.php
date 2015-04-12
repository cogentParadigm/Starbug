<?php
class hook_store_materialized_path extends QueryHook {
	function validate(&$query, $key, $value, $column, $argument) {
		if (empty($value)) $query->set($argument, "");
		else {
			$parent = get($query->model, $value);
			$query->set($argument, (empty($parent[$argument]) ? '-' : $parent[$argument]).$parent['id']."-");
		}
		return $value;
	}
}
?>
