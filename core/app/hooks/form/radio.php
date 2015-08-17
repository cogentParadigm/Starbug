<?php
class hook_form_radio extends FormHook {
	function build($form, &$control, &$field) {
		$field['type'] = 'radio';
		if ($form->get($field['name']) == $field['value']) $field['checked'] = 'checked';
		$control = "input";
	}
}
?>
