<?php
class hook_store_ordered {
	var $conditions = false;
	var $value = false;
	var $increment = 1;
	function set_conditions($query, $column, $argument, $value="insert") {
		if (false === $this->conditions) {
			$this->conditions = array();
			if (!empty($argument)) {
				$fields = explode(" ", $argument);
				if ($value === "insert") {
					foreach ($fields as $field) if (isset($query->fields[$field])) $this->conditions[$query->model.".".$field] = $query->fields[$field];
				} else {
					$id = $query->getId();
					$row = query($query->model)->select($query->model.".*")->condition("id", $id)->one();
					$same_level = true;
					foreach ($fields as $field) {
						if (is_null($row[$field])) $row[$field] = "NULL";
						$this->conditions[$query->model.".".$field] = $row[$field];
						if (isset($query->fields[$field]) && $query->fields[$field] != $row[$field]) $same_level = false;
					}
					if ($same_level) $this->increment = ($row[$column] < $value) ? -1 : 1;
				}
			} else if ($value != "insert") {
				$id = $query->getId();
				$row = query($query->model)->select($query->model.".*")->condition("id", $id)->one();
				$this->increment = ($row[$column] < $value) ? -1 : 1;
			}
		}
	}
	function empty_before_insert(&$query, $column, $argument) {
		$query->set($column, $this->insert($query, $column, "", $column, $argument));
	}
	function insert(&$query, $key, $value, $column, $argument) {
		$this->set_conditions($query, $column, $argument, "insert");
		if (!empty($value) && is_numeric($value)) $this->value = $value;
		$h = query($query->model)->select("MAX(".$query->model.".$column) as highest")->conditions($this->conditions)->condition($query->model.".statuses.slug", "deleted", "!=")->one();
		return $h['highest']+1;
	}
	function update(&$query, $key, $value, $column, $argument) {
		$this->set_conditions($query, $column, $argument, $value);
		return $value;
	}
	function after_store(&$query, $key, $value, $column, $argument) {
		if (false !== $this->value) $value = $this->value;
		if (empty($value)) return;
		$select = array("id", $column);
		if (!empty($argument)) $select = array_merge($select, array_keys($this->conditions));
		$id = $query->getId();
		$row = array("id" => $id);
		$ids = array($row['id']);
		while (!empty($row)) {
			query($query->model)->condition("id", $row['id'])->set($column, $value)->raw()->update();
			$row = query($query->model)->select($select, $query->model)->conditions($this->conditions)->condition($query->model.".id", $ids, "!=")->condition($query->model.".statuses.slug", "deleted", "!=")->condition($query->model.".".$column, $value)->one();
			$ids[] = $row['id'];
			$value += $this->increment;
		}
	}
}
?>
