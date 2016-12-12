<div <?php echo $this->filter->attributes($display->attributes); ?>>
	<?php foreach ($display->fields as $name => $field) { ?>
			<div <?php $field['attributes']['class'] = implode(' ',$field['attributes']['class']); echo $this->filter->attributes($field['attributes']); ?>>
				<?php foreach ($field as $key => $value) if($key != 'attributes') echo (string) $display->cells[$key]; ?>
			</div>
	<?php } ?>
</div>
