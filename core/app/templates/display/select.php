<select <?php html_attributes(array_merge($display->attributes, $display->options['attributes'])); ?>>
			<option value=""></option>
		<?php foreach ($display->items as $item) { ?>
			<option value="<?= htmlentities($item['id']); ?>"<? if ((is_array($display->options['value']) && in_array($item['id'], $display->options['value'])) || ($item['id'] == $display->options['value'])) echo " selected=\"true\""; ?>><?= $item['label']; ?></option>
		<?php } ?>
</select>
