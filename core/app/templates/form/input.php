<?php if ($type == "file") { ?>
	<?php
		$files = "[]";
		$column_info = sb($form->model)->hooks[$field];
		if ($column_info['type'] == "files") {
			$table = $form->model."_".$field;
			$records = query($table)->condition("files_id.id", $value)->select($table."_files_id.*")->sort($table.".position")->all();
		} else {
			$records = query("files")->condition("id", $value)->all();
		}
		if (count($records)) $files = str_replace('"', "'", json_encode($records));
		$attrs = 'data-dojo-type="starbug/form/FileSelect" data-dojo-props="input_name:\''.$attributes['name'].'\', files:'.$files;
		if (isset($attributes["size"])) $attrs .= ', size:'.$attributes['size'];
		$attrs .= '"';
	?>
    <div <?php echo $attrs; ?>></div>
<?php }  else { ?>
<?php if ($type === "checkbox") { ?>
	<input <? html_attributes(array("type" => "hidden", "id" => $attributes["id"]."-hidden", "value" => 0, "name" => $attributes['name'])); ?>/>
<?php } ?>
<input <? html_attributes($attributes); ?>/>
<?php } ?>
