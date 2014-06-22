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
			if ($query->mode == "update") $exists->condition($query->model.".id", $query->getId(), "!=");
			return $exists;				
	}
}
?>
