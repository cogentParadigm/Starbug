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
	
	function after_store(&$query, $key, $value, $column, $argument) {
		$existing = query("terms_index");
		$existing->condition("type", $query->model);
		$existing->condition("rel", $column);
		$existing->condition("type_id", $query->getId());
		$result = $existing->one();
		
		$existing->set("type", $query->model);
		$existing->set("rel", $column);
		$existing->set("type_id", $query->getId());
		$existing->set("terms_id", $value);
		if (empty($result)) {
			$existing->insert();
		} else {
			$existing->update();
		}
	}
}
?>
