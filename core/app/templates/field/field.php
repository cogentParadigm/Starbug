<div class="field field-name-<?php echo $field; ?>">
	<?php if (!$options["nolabel"]) { ?><div class="field-label"><?php echo $options['label']; ?></div><?php } ?>
	<?php render(array($model."/field/".$field."-".$options["formatter"], "field/".$field."-".$options["formatter"], $model."/field/".$options["formatter"], "field/".$options["formatter"], "field/text")); ?>
</div>
