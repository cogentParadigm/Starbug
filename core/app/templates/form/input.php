<?php if ($type == "file") { $file = query("files", "where:id=?  limit:1", array($form->get($field))); ?>
	<?php
		$attrs = 'data-dojo-type="starbug/form/FileSelect" data-dojo-props="input_name:\''.$attributes['name'].'\'';
		if (!empty($file)) {
			$attrs .= ', files:['.str_replace('"', "'", json_encode($file)).']';
		}
		$attrs .= '"';
	?>
    <div <?php echo $attrs; ?>></div>
<?php }  else { ?>
<?php if ($type === "checkbox") { ?>
	<input <? html_attributes(array("type" => "hidden", "id" => $attributes["id"]."-hidden", "value" => 0, "name" => $attributes['name'])); ?>/>
<?php } ?>
<input <? html_attributes($attributes); ?>/>
<?php } ?>
