<?php if(isset($attributes['multiple'])) $attributes['name'] .= '[]'; ?>
<?php
if ($mode == "display") {
	$this->render_display("list", $from, $query, array("attributes" => $attributes, "value" => $value, "template" => "select", "optional" => $optional));
} else { ?>
<select <? html_attributes($attributes); ?>>
	<? foreach ($options as $caption => $val) { ?>
		<option value="<?= htmlentities($val); ?>"<? if ((is_array($value) && in_array($val, $value)) || ($val === $value)) echo " selected=\"true\""; ?>><?= $caption; ?></option>
	<? } ?>
</select>
<?php } ?>
