<div <?php html_attributes($display->attributes); ?>>
	<?php foreach ($display->fields as $name => $field) { ?>
			<div <?php $field['attributes']['class'] = implode(' ',$field['attributes']['class']); html_attributes($field['attributes']); ?>>
				<?php foreach ($field as $key => $value) if($key != 'attributes') echo (string) $display->cells[$key]; ?>
			</div>
	<?php } ?>
</div>
