<form <?php html_attributes($display->attributes); ?>>
	<?php if (success($display->model, $display->action)) { ?>
		<p class="alert alert-success">Saved</p>
	<?php } ?>
	<?php if (errors($display->model."[global]")) { ?>
		<?php foreach (sb()->errors[$display->model]["global"] as $key => $value) { ?>
			<p class="alert alert-danger"><?php echo $value; ?></p>
		<?php } ?>
	<?php } ?>
<?php if ($display->method == "post") { ?>
	<input class="postback" name="postback" type="hidden" value="<?php echo $display->postback; ?>" />
<?php } ?>
<?php if (!empty($display->action)) { ?>
	<input class="action" name="action[<?php echo $display->model; ?>]" type="hidden" value="<?php echo $display->action; ?>" />
<?php } ?>
<?php if ($display->method == "post") { ?>
	<input name="oid" type="hidden" value="<?php echo filter_string($_COOKIE['oid']); ?>"/>
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
	<div class="row form-actions">
		<div class="col-sm-12">
			<div class="btn-group">
				<?php button($display->submit_label, "class:btn-success  name:operation  value:save"); ?>
				<?php //button("Save and add another", "class:btn-success  name:operation  value:save_add_another"); ?>
				<?php if (!empty($display->options['cancel_url'])) { ?>
					<button type="button" class="cancel btn btn-danger" onclick="window.location='<?php echo uri($display->options['cancel_url']); ?>'">Cancel</button>
				<?php } ?>
			</div>
		</div>
	</div>
</form>
