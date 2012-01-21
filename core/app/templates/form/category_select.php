<?php
	if (reset($options) == -1) $value = -1;
	render(array($model."/form/$field-select", "form/$field-select", $model."/form/select", "form/select"));
?>
<?php if (end($options) == -1) { ?>
	<div id="<?php echo $id; ?>_new_category"<?php if ($value != -1) echo ' style="display:none"'; ?>>
		<? echo $form->text($field."_new_category  label:New Category"); ?>
		<br class="clear"/>
	</div>
<?php } ?>
