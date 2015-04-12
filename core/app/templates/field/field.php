<div class="display-field display-field-<?php echo $field; ?>">
	<?php if (!$options["nolabel"]) { ?><div class="display-field-label"><?php echo $options['label']; ?></div><?php } ?>
	<?php $this->render(array($model."/field/".$field."-".$options["formatter"], "field/".$field."-".$options["formatter"], $model."/field/".$options["formatter"], "field/".$options["formatter"], "field/text")); ?>
</div>
