<?php if(isset($attributes['multiple'])) $attributes['name'] .= '[]'; ?>
<?php
if ($mode == "display") {
	$this->displays->render("DropdownDisplay", array("value" => $value, "attributes" => $attributes, "model" => $from, "action" => $query, "optional" => $optional, "other_option" => $other_option));
} else { ?>
	<?php
		$other_id = $this->filter->normalize($name)."_other";
	?>
<select <?php echo $this->filter->attributes($attributes); ?><?php if ($other_option) { ?> onchange="var text = document.getElementById('<?php echo $other_id; ?>_text'); if (this.options[this.selectedIndex].hasAttribute('data-other')) text.style.display = 'block'; else text.style.display='none';"<?php } ?>>
	<?php $found = false; foreach ($options as $caption => $val) { ?>
		<?php
			$selected = "";
			if ((is_array($value) && in_array($val, $value)) || ($val === $value)) {
				$found = true;
				$selected = ' selected="selected"';
			}
		?>
		<option value="<?php echo htmlentities($val); ?>"<?php echo $selected; ?>><?php echo $caption; ?></option>
	<?php } ?>
	<?php if (!empty($other_option)) { ?>
		<?php
			$other_value = "";
			if (!$found) {
				$other_value = $value;
			}
			?>
			<option id="<?php echo $other_id; ?>" data-other value="<?php echo $other_value; ?>"<?php if (!empty($other_value)) echo " selected=\"true\""; ?>><?php echo $other_option; ?></option>
	<?php } ?>
</select>
	<?php if (!empty($other_option)) { ?>
		<input id="<?php echo $other_id; ?>_text" type="text" style="margin-top:5px;<?php if (empty($other_value)) echo 'display:none'; ?>" value="<?php echo $other_value; ?>" oninput="var op = document.getElementById('<?php echo $other_id; ?>');op.value = this.value;op.parent.selectedIndex = op.parent.options.indexOf(op);" class="form-control"/>
	<?php } ?>
<?php } ?>
