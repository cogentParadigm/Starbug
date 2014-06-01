<?php
class hook_store_selection {
	function after_store(&$query, $key, $value, $column, $argument) {
		if (empty($value)) return;
		$id = $query->getId();
		$conditions = array("`".$column."`" => 1);
		if (!empty($argument)) {
			$fields = explode(" ", $argument);
			$row = query($query->model)->condition("id", $id)->one();
			foreach($fields as $field) $conditions[$field] = $row[$field];	
		}
		query($query->model)->conditions($conditions)->condition("id", $id, "!=")->set($column, 0)->update();
	}
}
?>
