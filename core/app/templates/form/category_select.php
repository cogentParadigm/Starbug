<select <? html_attributes($attributes); ?>>
	<? foreach ($terms as $term) { ?>
		<option value="<?= $term['id']; ?>"<? if ((is_array($value) && (in_array($term['id'], $value) || in_array($term['slug'], $value))) || ($term['id'] == $value || $term['slug'] == $value)) echo " selected=\"true\""; ?>><?= $term['term']; ?></option>
	<? } ?>
</select>
