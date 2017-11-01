<?php
namespace Starbug\Core;
class StoreOperationHook extends QueryHook {
	protected $replace = false;
	function __construct(ModelFactoryInterface $models, DatabaseInterface $db) {
		$this->models = $models;
		$this->db = $db;
	}
	function validate($query, $key, $value, $column, $argument) {
		if (is_array($value)) {
			$hooks = $this->models->get($query->model)->hooks[$column];
			if ($this->models->has($hooks["type"])) {
				$model = $hooks["type"];
				$multiple = true;
			} else {
				$model = reset(explode(" ", $hooks["references"]));
				$multiple = false;
			}
			$instance = $this->models->get($model);
			$instance->$argument($value);
			if (!$instance->errors()) {
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
}
