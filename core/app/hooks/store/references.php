<?php
namespace Starbug\Core;
class hook_store_references extends QueryHook {
	protected $replace = false;
	function __construct(ModelFactoryInterface $models, DatabaseInterface $db) {
		$this->models = $models;
		$this->db = $db;
	}
	function validate(&$query, $key, $value, $column, $argument) {
		if ($query->unvalidated[$key] === "") {
			$this->replace = true;
		} else if (is_array($value)) {
			$model = reset(explode(" ", $argument));
			$instance = $this->models->get($model);
			$instance->post("create", $value);
			if ($instance->success("create")) {
				$value = empty($value["id"]) ? $instance->insert_id : $value["id"];
			} else {
				$errors = $this->db->errors[$model];
				unset($this->db->errors[$model]);
				foreach ($errors as $field => $e) {
					$this->db->errors[$query->model][$key.".".$field] = $e;
				}
			}
		}
		return $value;
	}
	function store(&$query, $key, $value, $column, $argument) {
		$model = reset(explode(" ", $argument));
		return ($this->replace && !is_null($this->models->get($model)->insert_id)) ? $this->models->get($model)->insert_id : $value;
	}
}
?>
