<form <?php html_attributes($display->attributes); ?>>
	<?php if (!empty($display->model) && !empty($display->default_action) && $display->success($display->default_action)) { ?>
		<p class="alert alert-success">Saved</p>
	<?php } ?>
	<?php if ($display->errors("global")) { ?>
		<?php foreach ($display->errors("global", true) as $key => $value) { ?>
			<p class="alert alert-danger"><?php echo $value; ?></p>
		<?php } ?>
	<?php } ?>
<?php if ($display->method == "post") { ?>
	<input name="oid" type="hidden" value="<?php echo filter_string($request->getCookie('oid')); ?>"/>
<?php } ?>
<?php $item_id = $display->get("id"); if (!empty($item_id)) { ?>
	<input id="id" name="<?php echo $display->model; ?>[id]" type="hidden" value="<?php echo filter_string($display->get('id')); ?>" />
<?php } ?>
<?php
	if (!$display->layout->is_empty()) {
		foreach ($display->fields as $name => $field) {
			$display->layout->append($field['pane'], $display->form_control($field['input_type'], array_merge(array($name), $field)));
		}
		$display->layout->render();
	} else {
		foreach ($display->fields as $name => $field) {
			$this->assign("display", $display);
			echo $display->form_control($field['input_type'], array_merge(array($name), $field));
		}
	}
?>
<?php if ($display->actions->template != "inline") { ?>
	<div class="row form-actions">
		<div class="col-sm-12">
			<div class="btn-group">
<?php } ?>
				<?php
					foreach ($display->actions->fields as $name => $field) {
						$field['value'] = $name;
						$field['name'] = 'action';
						if (!empty($display->model)) $field['name'] .= '['.$display->model.']';
						$label = $field['label'];
						button($label, $field);
					}
				?>
				<?php if (!empty($display->cancel_url)) { ?>
					<button type="button" class="cancel btn btn-danger" onclick="window.location='<?php echo $this->url->build($display->cancel_url); ?>'">Cancel</button>
				<?php } ?>
<?php if ($display->actions->template != "inline") { ?>
			</div>
		</div>
	</div>
<?php } ?>
</form>
