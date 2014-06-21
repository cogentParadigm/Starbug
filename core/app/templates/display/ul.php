<ul <?php html_attributes($display->attributes); ?>>
		<?php foreach ($display->items as $item) { ?>
			<li>
			<?php foreach ($display->fields as $name => $field) render_field($model, $item, $name, $field); ?>
			</li>
		<?php } ?>
</ul>
