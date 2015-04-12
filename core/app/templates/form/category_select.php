<select <?php html_attributes($attributes); ?>>
	<?php foreach ($terms as $term) { ?>
		<option data-slug="<?php echo $term['slug']; ?>" value="<?php echo $term['id']; ?>"<?php if ((is_array($value) && (in_array($term['id'], $value) || in_array($term['slug'], $value))) || ($term['id'] == $value || $term['slug'] == $value)) echo " selected=\"true\""; ?>><?php echo $term['term']; ?></option>
	<?php } ?>
</select>
