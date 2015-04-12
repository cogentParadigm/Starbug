<div class="table-responsive">
	<table <?php html_attributes($display->attributes); ?>>
		<thead>
			<tr>
				<?php foreach ($display->fields as $name => $field) { ?>
					<th><?php echo $field['label']; ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($display->items as $item) { ?>
				<tr>
					<?php foreach ($display->fields as $name => $field) { ?>
						<td class="display-field display-field-<?php echo $name; ?>"><?php render_field($model, $item, $name, $field + array("nolabel" => true)); ?></td>
					<?php } ?>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
