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
<input <? html_attributes($attributes); ?>/>
<?php } ?>