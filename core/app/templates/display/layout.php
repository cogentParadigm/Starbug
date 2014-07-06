<div <?php html_attributes($display->attributes); ?>>
	<?php foreach ($display->fields as $name => $field) { ?>
			<div class="row">
				<?php foreach ($field as $key => $value) echo (string) $display->cells[$key]; ?>
			</div>
	<?php } ?>
</div>
