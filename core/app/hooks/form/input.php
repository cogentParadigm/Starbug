<?php
class hook_form_input extends FormHook {
	function build($form, &$control, &$field) {
		$var = $form->get($field['name']);
		if (!empty($var)) $field['value'] = htmlentities($var, ENT_QUOTES, "UTF-8");
		else if (!empty($field['default'])) {
			$field['value'] = $field['default'];
			unset($field['default']);
		}
	}
}
?>
