<?php if(isset($attributes['multiple'])) $attributes['name'] .= '[]'; ?>
<?php
if ($mode == "display") {
	render_display("list", $from, $query, array("attributes" => $attributes, "value" => $value, "template" => "select", "optional" => $optional));
	assign("display", $display);
} else { ?>
<select <? html_attributes($attributes); ?>>
	<? foreach ($options as $caption => $val) { ?>
		<option value="<?= htmlentities($val); ?>"<? if ((is_array($value) && in_array($val, $value)) || ($val === $value)) echo " selected=\"true\""; ?>><?= $caption; ?></option>
	<? } ?>
</select>
<?php } ?>
