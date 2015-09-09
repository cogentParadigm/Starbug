<?php
namespace Starbug\Core;
class hook_store_category extends QueryHook {
	protected $models;
	protected $db;
	function __construct(DatabaseInterface $db, ModelFactoryInterface $models) {
		$this->db = $db;
		$this->models = $models;
	}
	function validate($query, $key, $value, $column, $argument) {
		if (!empty($value) && !is_numeric($value)) {
			$field = $this->models->get($query->model)->hooks[$column];
			$taxonomy = (empty($field["taxonomy"])) ? $query->model."_".$column : $field['taxonomy'];
			$term = $this->db->query("terms")->condition("term", $value)->orCondition("slug", $value)->one();
			if ($term) $value = $term["id"];
		}
		return $value;
	}
	function store(&$query, $key, $value, $column, $argument) {
		if (!empty($value) && !is_numeric($value)) $this->models->get($query->model)->error("Term not valid", $column);
		return $value;
	}
}
?>
