<?php $item_id = $display->get("id"); if (!empty($item_id)) { ?>
	<input id="id" name="<?php echo $display->get_name("id"); ?>" type="hidden" value="<?php echo $this->filter->string($display->get('id')); ?>" />
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
			echo $display->form_control($field['input_type'], array_merge(array($name), $field))."\n";
		}
	}
?>
