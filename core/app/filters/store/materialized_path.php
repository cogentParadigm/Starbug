<?php
//default
foreach($args as $parent_id_field => $path_field) {
	if (empty($fields[$parent_id_field])) $fields[$path_field] = "";
	else {
		$parent = get($name, $fields[$parent_id_field]);
		$fields[$path_field] = (empty($parent[$path_field]) ? '-' : $parent[$path_field]).$parent['id']."-";
	}
}
?>
