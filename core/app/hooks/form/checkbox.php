<?php
class hook_form_checkbox extends FormHook {
	function build($form, &$control, &$field) {
		$field['type'] = 'checkbox';
		$value = $form->get($field['name']);
		if (($value === '' && $field['value'] == $field['default']) || $value == $field['value']) $field['checked'] = 'checked';
		$control = 'input';
	}
}
?>
