<?php
class hook_store_category {
	function validate($query, $key, $value, $column, $argument) {
		if (!empty($value) && !is_numeric($value)) {
			$field = sb($query->model)->hooks[$column];
			$taxonomy = (empty($field["taxonomy"])) ? $query->model."_".$column : $field['taxonomy'];
			$term = query("terms")->condition("term", $value)->orCondition("slug", $value)->one();
			if ($term) $value = $term["id"];
		}
		return $value;
	}
	function store(&$query, $key, $value, $column, $argument) {
		if (!empty($value) && !is_numeric($value)) error("Term not valid", $column);
		return $value;
	}
}
?>
