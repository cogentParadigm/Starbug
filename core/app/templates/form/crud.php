	<?php
		$items = "[]";
		$table = $attributes['table'];
		$model_id = $display->get("id");
		$records = array();
		if ($model_id) $records = query($table)->condition($display->model."_id", $model_id)->sort($table.".position")->group($display->model."_id")->select("GROUP_CONCAT(id) as id")->one();
		if (!empty($records)) $items = "[".$records['id']."]";
		$attrs = 'data-dojo-type="starbug/form/CRUDSelect" data-dojo-props="input_name:\''.$attributes['name'].'\', model:\''.$table.'\', value:'.$items;
		if (isset($attributes["size"])) $attrs .= ', size:'.$attributes['size'];
		$attrs .= '"';
	?>
    <div <?php echo $attrs; ?>></div>
