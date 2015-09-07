<?php
namespace Starbug\Core;
class hook_store_type extends QueryHook {
	public function __construct(ModelFactoryInterface $models) {
		$this->models = $models;
	}
	function empty_validate(&$query, $column, $argument) {
		if ($this->models->has($argument)) $query->exclude($column);
	}
	function validate(&$query, $key, $value, $column, $argument) {;
		if ($this->models->has($argument)) $query->exclude($key);
		return $value;
	}
	function after_store(&$query, $key, $value, $column, $argument) {
		if ($argument == "terms" || $argument == "blocks" || !$this->models->has($argument) || empty($value)) return;

		//vars
		$model = $query->model;
		$model_id = $query->getId();
		$hooks = $this->models->get($model)->hooks[$column];
		$target = (empty($hooks['table'])) ? $model."_".$column : $hooks['table'];
		$type = $argument;
		$type_ids = array();
		$ids = array();
		$clean = false;

		//loop through values
		if (!is_array($value)) $value = explode(",", preg_replace("/[,\s]+/", ",", $value));
		foreach ($value as $position => $type_id) {
			$remove = false;
			$value_type = ($type == $target) ? "id" : $column."_id";
			if (is_array($type_id)) {
				$value_type = "object";
			} else {
				if (0 === strpos($type_id, "-")) {
					$remove = true;
					$type_id = substr($type_id, 1);
				}
				if (0 === strpos($type_id, "#")) {
					$value_type = "id";
					$type_id = substr($type_id, 1);
				}
			}

			if ($remove && $type_id === "~") {
				$clean = true;
			} else if ($value_type === "object") {
				if (isset($type_id['id'])) {
					$entry = query($target)->condition("id", $type_id['id']);
					$ids[] = $type_id['id'];
				} else {
					$entry = query($target)->conditions(array($model."_id" => $model_id, $column."_id" => $type_id[$column."_id"]));
					$type_ids[] = $type_id[$column."_id"];
				}
				$entry->set($model."_id", $model_id);
				$entry->fields($type_id);
				$entry->set("position", intval($position)+1);
				if (isset($type_id['id']) || $entry->one()) $entry->update();
				else $entry->insert();
			} else if ($value_type === "id") {
				$entry = query($target)->condition("id", $type_id);
				if ($remove) {
					$entry->delete();
				} else {
					//update
					$entry->set($model."_id", $model_id);
					$entry->set("position", intval($position)+1);
					$entry->update();
					$ids[] = $type_id;
				}
			} else {
				$entry = query($target)->conditions(array($model."_id" => $model_id, $column."_id" => $type_id));
				if ($remove) {
					//remove
					$entry->delete();
				} else {
					//add
					$entry->set($model."_id", $model_id);
					$entry->set($column."_id", $type_id);
					$entry->set("position", intval($position)+1);
					if ($entry->one()) $entry->update();
					else $entry->insert();
					$type_ids[] = $type_id;
				}
			}
		}

		//clean
		if ($clean) {
			$query = query($target)->condition($model."_id", $model_id);
			if (!empty($type_ids)) {
				$query->condition($column."_id", $type_ids, "!=");
			}
			if (!empty($ids)) {
				$query->condition("id", $ids, "!=");
			}
			$query->delete();
		}
	}
}
?>
