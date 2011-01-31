<?php
foreach ($args as $field => $ordered) {
	if (empty($fields['id'])) {
		$h = $this->query($name, "select:MAX(`$field`) as highest  limit:1");
		$fields[$field] = $h['highest']+1;
	} else {
		if (!empty($fields[$field])) {
			$x = $fields[$field];
			$old = $this->query($name, "select:`$field`  where:`id`='$fields[id]'  limit:1");
			$increment = ($old[$field] < $x) ? -1 : 1;
			$row = $this->query($name, "where:`id`!='$fields[id]' && `$field`='$x'  limit:1");
			while (!empty($row)) {
				$x += $increment;
				$this->queue($name, "id:$row[id]  $field:$x");
				$row = $this->query($name, "where:`id`!='$fields[id]' && `$field`='$x'  limit:1");
			}
		}
	}
}
?>
