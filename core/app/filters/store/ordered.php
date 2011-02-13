<?php
foreach ($args as $field => $ordered) {
	$ordered_items = $name."_".$field."_".$order;
	global $$ordered_items;
	if ($storing) {
		if (!empty($$ordered_items)) $$ordered_items = "";
	} else {
		$on_store = true;
		if (empty($fields['id'])) {
			$h = $this->query($name, "select:MAX(`$field`) as highest  limit:1");
			$fields[$field] = $h['highest']+1;
		} else if (!empty($fields[$field])) {
			$x = $fields[$field];
			$old = $this->query($name, "select:`$field`  where:`id`='$fields[id]'  limit:1");
			$increment = ($old[$field] < $x) ? -1 : 1;
			if (empty($$ordered_items)) $$ordered_items = $fields['id'];
			else $$ordered_items .= ",".$fields['id'];
			$row = $this->query($name, "where:`id` NOT IN (".$$ordered_items.") && `$field`='$x'  limit:1");
			if (!empty($row)) $this->queue($name, "id:$row[id]  $field:".($x+$increment));
		}
	}
}
?>
