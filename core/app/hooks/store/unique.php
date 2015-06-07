<?php
class hook_store_unique extends QueryHook {
	function __construct(DatabaseInterface $db, ModelFactoryInterface $models) {
		$this->db = $db;
		$this->models = $models;
	}
	function validate(&$query, $key, $value, $column, $argument) {
		$argument = explode(" ", $argument);
		$existing = $this->db->query($query->model)->select("id")->select($column)->condition($column, $value);
		foreach ($argument as $c) if (!empty($c)) $existing->condition($c, $query->fields[$c]);
		$row = $existing->one();
		$id = $query->getId();
		if ($row && (empty($id) || $id != $row["id"])) $this->models->get($query->model)->error("That $column already exists.", $column);
		return $value;
	}
}
?>
