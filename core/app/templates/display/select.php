<select <?php html_attributes($display->attributes); ?>>
			<option value=""><?php if (!empty($display->options['optional'])) echo $display->options['optional']; ?></option>
		<?php foreach ($display->items as $item) { ?>
			<option value="<?php echo htmlentities($item['id']); ?>"<?php if ((is_array($display->options['value']) && in_array($item['id'], $display->options['value'])) || ($item['id'] == $display->options['value'])) echo " selected=\"true\""; ?>><?php echo $item['label']; ?></option>
		<?php } ?>
</select>
