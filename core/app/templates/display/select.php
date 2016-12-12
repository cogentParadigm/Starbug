<?php
	$other_id = $this->filter->normalize($display->attributes['name'])."_other";
	$found = false;
?>
<select <?php echo $this->filter->attributes($display->attributes); ?><?php if ($display->options['other_option']) { ?> onchange="var text = document.getElementById('<?php echo $other_id; ?>_text'); if (this.options[this.selectedIndex].hasAttribute('data-other')) text.style.display = 'block'; else text.style.display='none';"<?php } ?>>
	<?php if (false !== $display->options['optional']) { ?>
		<option value=""><?php if (!empty($display->options['optional'])) echo $display->options['optional']; ?></option>
	<?php } ?>
	<?php foreach ($display->items as $item) { ?>
		<?php
			$selected = "";
			if ((is_array($display->options['value']) && in_array($item['id'], $display->options['value'])) || ($item['id'] == $display->options['value'])) {
				$found = true;
				$selected = ' selected="selected"';
			}
		?>
		<option value="<?php echo htmlentities($item['id']); ?>"<?php echo $selected; ?>><?php echo $item['label']; ?></option>
	<?php } ?>
	<?php if (!empty($display->options['other_option'])) { ?>
		<?php
			$other_value = "";
			if (!$found) {
				$other_value = $display->options['value'];
			}
		?>
		<option id="<?php echo $other_id; ?>" data-other value="<?php echo $other_value; ?>"<?php if (!empty($other_value)) echo " selected=\"true\""; ?>><?php echo $other_option; ?></option>
	<?php } ?>
</select>
<?php if (!empty($display->options['other_option'])) { ?>
	<input id="<?php echo $other_id; ?>_text" type="text" style="margin-top:5px;<?php if (empty($other_value)) echo 'display:none'; ?>" value="<?php echo $other_value; ?>" oninput="var op = document.getElementById('<?php echo $other_id; ?>');op.value = this.value;op.parent.selectedIndex = op.parent.options.indexOf(op);" class="form-control"/>
<?php } ?>
