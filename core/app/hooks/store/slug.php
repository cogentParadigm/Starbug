<?php
//stores a URL path slug
class hook_store_slug {
	function empty_before_insert(&$query, $column, $argument) {
		$query->set($column, $this->validate($query, $column, "", $column, $argument));
	}
	function validate(&$query, $key, $value, $column, $argument) {
		if (empty($value) && isset($query->fields[$argument])) {
			$value = $query->fields[$argument];
		}
		$value = $base = strtolower(str_replace(" ", "-", normalize($value)));

		$exists = $this->exists($query, $column, $value);
		$count = 2;
		while ($exists->one()) {
			$value = $base."-".$count;
			$exists = $this->exists($query, $column, $value);
			$count++;
		}
		return $value;
	}
	
	function exists($query, $column, $value) {
			$exists = query($query->model)->condition($query->model.".".$column, $value);
			$id = 0; $record = false;
			if ($query->mode == "update") {
				$id = $query->getId();
				//$record = query($query->model)->condition("id", $id)->one();
				$exists->condition($query->model.".id", $id, "!=");
			}
			if (!empty(sb($query->model)->hooks[$column]["unique"])) {
				$parts = explode(" ", sb($query->model)->hooks[$column]["unique"]);
				foreach ($parts as $c) if (!empty($c)) $exists->condition($c, $query->fields[$c]);
			}
			return $exists;				
	}
}
?>
