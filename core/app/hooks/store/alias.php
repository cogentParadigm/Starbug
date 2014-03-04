<?php
class hook_store_alias {
	function validate(&$query, $key, $value, $column, $alias) {
		if (!empty($value) && !is_numeric($value)) {
			$referenced_model = explode(" ", schema($query->model.".fields.".$column.".filters.references"));
			// $alias might be '%first_name% %last_name%'
			$alias = explode("%", $alias);
			$match = '';
			$num = 1;
			while (!empty($alias)) {
				$next = array_pop($alias);
				if ($num % 2 == 0) { //match column
					if (empty($match)) $match = "$next";
					else $match = "concat($next, $match)";
				} else if (!empty($next)) { // in between string
					if (empty($match)) $match = "'$next'";
					else $match = "concat('$next', $match)";
				}
				$num++;
			}
			$row = query($referenced_model[0])->select($referenced_model[1])->condition($match, $value)->one();
			$value = $row[$referenced_model[1]];
		}
		return $value;
	}
}
?>
