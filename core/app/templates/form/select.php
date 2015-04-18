<?php if(isset($attributes['multiple'])) $attributes['name'] .= '[]'; ?>
<?php
if ($mode == "display") {
	$this->render_display("DropdownDisplay", array("value" => $value, "attributes" => $attributes, "model" => $from, "action" => $query, "optional" => $optional));
} else { ?>
<select <?php html_attributes($attributes); ?>>
	<?php foreach ($options as $caption => $val) { ?>
		<option value="<?php echo htmlentities($val); ?>"<?php if ((is_array($value) && in_array($val, $value)) || ($val === $value)) echo " selected=\"true\""; ?>><?php echo $caption; ?></option>
	<?php } ?>
</select>
<?php } ?>
