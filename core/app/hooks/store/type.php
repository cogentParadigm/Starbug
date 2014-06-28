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
		if ($argument == "terms" || !sb()->db->has($argument)) return;
		
		//vars
		$model = $query->model;
		$model_id = $query->getId();
		$target = $model."_".$column;
		$type = $argument;
		$ids = array();
		$clean = false;
		
		//loop through values
		if (!is_array($value)) $value = explode(",", $value);
		foreach ($value as $position => $type_id) {
			$entry = query($target)->conditions(array($model."_id" => $model_id, $type."_id" => $type_id));
			if ($id == "-~") $clean = true;
			else if (0 === strpos($id, "-")) {
				//remove
				$entry->delete();
			} else {
				//add
				$entry->set($model."_id", $model_id);
				$entry->set($type."_id", $type_id);
				$entry->set("position", $position);
				if ($entry->one()) $entry->update();
				else $entry->insert();
				$ids[] = $entry->getId();
			}
		}
		
		//clean
		if ($clean) {
			$query = query($target)->condition($model."_id", $model_id);
			if (!empty($ids)) {
				$query->condition($type."_id", $ids, "!=");
			}
			$query->delete();
		}
	}
}
?>
