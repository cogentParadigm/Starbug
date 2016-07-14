<?php if ($nolabel === false) { ?><label for="<?php echo $id; ?>"<?php if ($display->horizontal) { ?> class="col-sm-3"<?php } ?>>
	<?php if ($control == "input" && ($type == "checkbox" || $type == "radio")) $this->render(array("$model/form/$field-$control", "form/$field-$control", "$model/form/$control", "form/$control")); ?>
	<?php echo $label; if ($required) echo '<span class="asterisk">*</span>'; ?>
</label><?php } ?>
