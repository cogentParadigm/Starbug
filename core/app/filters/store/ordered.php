<?php
foreach ($args as $field => $ordered) {
	$where = "";
	if (!empty($ordered)) {
		$ordered = explode(" ", $ordered);
		foreach ($ordered as $o) $where .= $o."='".$fields[$o]."' && ";
	}
	$varname = "_".$field;
	if (!empty($$varname)) {
		$fields[$field] = $$varname;
	} else if (!empty($fields[$varname])) {
		$$varname = $fields[$varname];
		unset($fields[$varname]);
	}
	if (empty($fields['id'])) {
		if (!empty($fields[$field]) && is_numeric($fields[$field])) {
			$after_store = true;
			if (!$storing) $fields[$varname] = $fields[$field];
		}
		$h = $this->query($name, "select:MAX(`$field`) as highest  where:$where"."1  limit:1");
		$fields[$field] = $h['highest']+1;
		unset($errors[$field]['required']);
	} else if (is_numeric($fields[$field])) {
		$x = $fields[$field];
		unset($fields[$field]);
		$select = array("id", $field);
		if (!empty($ordered)) $select = array_merge($select, $ordered);
		$row = $this->query($name, "select:".implode(",", $select)."  where:id='$fields[id]'  limit:1");
		$same_level = true;
		if (!empty($ordered)) {
			foreach ($ordered as $o) if ($row[$o] != $fields[$o]) $same_level = false;
		}
		$ids = $row['id'];
		if ($same_level) $increment = ($row[$field] < $x) ? -1 : 1;
		else $increment = 1;
		while (!empty($row)) {
			raw_query("UPDATE ".P($name)." SET $field='$x' where id='$row[id]'");
			$row = $this->query($name, "where:$where"."id NOT IN ($ids) && `$field`='$x'  limit:1");
			$ids .= ", ".$row['id'];
			$x += $increment;
		}
	}
}
?>
