<div class="field field-name-<?php echo $field; ?>">
	<div class="field-label"><?php echo $options['label']; ?></div>
	<?php render(array($model."/field/".$field."-".$options["template"], "field/".$field."-".$options["template"], $model."/field/".$options["template"], "field/".$options["template"], "field/text")); ?>
</div>
