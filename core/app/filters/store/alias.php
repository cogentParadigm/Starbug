<?php
foreach ($args as $field => $alias) {
	if ((!empty($fields[$field])) && (!is_numeric($fields[$field]))) {
		$referenced_model = explode(" ", $byfilter['references'][$field]);
		// $alias might be '%first_name% %last_name%
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
		$row = query($referenced_model[0], "select:$referenced_model[1]  where:$match='$fields[$field]'  limit:1");
		$fields[$field] = $row[$referenced_model[1]];
	} else if (isset($fields[$field])) $fields[$field] = "NULL";
}
?>
