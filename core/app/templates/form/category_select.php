<select <? html_attributes($attributes); ?>>
	<? foreach ($terms as $term) { ?>
		<option data-slug="<?php echo $term['slug']; ?>" value="<?= $term['id']; ?>"<? if ((is_array($value) && (in_array($term['id'], $value) || in_array($term['slug'], $value))) || ($term['id'] == $value || $term['slug'] == $value)) echo " selected=\"true\""; ?>><?= $term['term']; ?></option>
	<? } ?>
</select>
