<?php
foreach ($args as $field => $ordered) {
	if (!empty($ordered)) $where = $ordered."='".$fields[$ordered]."' && ";
	else $where = "";
	if (empty($fields['id'])) {
		$h = $this->query($name, "select:MAX(`$field`) as highest  where:$where"."1  limit:1");
		$fields[$field] = $h['highest']+1;
	} else if (!empty($fields[$field])) {
		$x = $fields[$field];
		unset($fields[$field]);
		$row = $this->query($name, "select:id,$field  where:id='$fields[id]'  limit:1");
		$ids = $row['id'];
		$increment = ($row[$field] < $x) ? -1 : 1;
		//$increment = 1;
		while (!empty($row)) {
			raw_query("UPDATE ".P($name)." SET $field='$x' where id='$row[id]'");
			$row = $this->query($name, "where:$where"."id NOT IN ($ids) && `$field`='$x'  limit:1");
			$ids .= ", ".$row['id'];
			$x += $increment;
		}
	}
}
?>
