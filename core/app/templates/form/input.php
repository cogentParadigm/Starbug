<?php if ($type == "file") { ?>
	<?php
		$files = "[]";
		$column_info = $this->models->get($display->model)->column_info($field);
		$records = array();
		if (!empty($value)) {
			if (!is_array($value)) $value = explode(",", preg_replace("/[,\s]+/", ",", $value));
			$records = $this->db->query("files")->condition("id", $value)->sort("FIELD(id, '".implode("','", $value)."')")->all();
		}
		if (count($records)) $files = str_replace('"', "'", json_encode($records));
		$attrs = 'data-dojo-type="starbug/form/FileSelect" data-dojo-props="input_name:\''.$attributes['name'].'\', files:'.$files;
		if (isset($attributes["size"])) $attrs .= ', size:'.$attributes['size'];
		$attrs .= '"';
	?>
    <div <?php echo $attrs; ?>></div>
<?php }  else { ?>
<?php if ($type === "checkbox") { ?>
	<input <?php html_attributes(array("type" => "hidden", "id" => $attributes["id"]."-hidden", "value" => 0, "name" => $attributes['name'])); ?>/>
<?php } ?>
<input <?php html_attributes($attributes); ?>/>
<?php } ?>
