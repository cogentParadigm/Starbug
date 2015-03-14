<? if ($nolabel === false) { ?><label for="<?= $id; ?>">
	<?php if ($control == "input" && ($type == "checkbox" || $type == "radio")) $this->render(array("$model/form/$field-$control", "form/$field-$control", "$model/form/$control", "form/$control")); ?>
	<?= $label; if ($required) echo '<span class="asterisk">*</span>'; ?>
</label><? } ?>
