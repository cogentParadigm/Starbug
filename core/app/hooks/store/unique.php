<?php
class hook_store_unique {
	function validate(&$query, $key, $value, $column, $argument) {
		$argument = explode(" ", $argument);
		$existing = query($query->model)->select("id")->select($column)->condition($column, $value);
		foreach ($argument as $c) if (!empty($c)) $existing->condition($c, $query->fields[$c]);
		$row = $existing->one();
		if ($row && (empty($query->fields["id"]) || $query->fields["id"] != $row["id"])) error("That $column already exists.", $column);
		return $value;
	}
}
?>
