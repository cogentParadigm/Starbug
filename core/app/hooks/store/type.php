<?php
class hook_store_type {
	function empty_validate(&$query, $column, $argument) {
		if (sb()->db->has($argument) || $argument == "category") $query->exclude($column);
	}
	function validate(&$query, $key, $value, $column, $argument) {;
		if (sb()->db->has($argument) || $argument == "category") $query->exclude($key);
		return $value;
	}
	function after_store(&$query, $key, $value, $column, $argument) {
		if ($argument == "terms" || !sb()->db->has($argument) || empty($value)) return;
		
		//vars
		$model = $query->model;
		$model_id = $query->getId();
		$hooks = sb($model)->hooks[$column];
		$target = (empty($hooks['table'])) ? $model."_".$column : $hooks['table'];
		$type = $argument;
		$type_ids = array();
		$ids = array();
		$clean = false;
		
		//loop through values
		if (!is_array($value)) $value = explode(",", $value);
		foreach ($value as $position => $type_id) {
			$remove = false;
			$value_type = ($type == $target) ? "id" : $type."_id";
			if (0 === strpos($type_id, "-")) {
				$remove = true;
				$type_id = substr($type_id, 1);
			}
			if (0 === strpos($type_id, "#")) {
				$value_type = "id";
				$type_id = substr($type_id, 1);
			}

			if ($remove && $type_id === "~") $clean = true;
			else if ($value_type === "id") {
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
				$entry = query($target)->conditions(array($model."_id" => $model_id, $type."_id" => $type_id));
				if ($remove) {
					//remove
					$entry->delete();
				} else {
					//add
					$entry->set($model."_id", $model_id);
					$entry->set($type."_id", $type_id);
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
				$query->condition($type."_id", $type_ids, "!=");
			}
			if (!empty($ids)) {
				$query->condition("id", $ids, "!=");
			}
			$query->delete();
		}
	}
}
?>
