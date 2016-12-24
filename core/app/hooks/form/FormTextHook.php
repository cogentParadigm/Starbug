<?php
namespace Starbug\Core;
class FormTextHook extends FormHook {
	function build($form, &$control, &$field) {
		$field['type'] = 'text';
		//POSTed or default value
		$var = $form->get($field['name']);
		if ($var != "") $field['value'] = htmlentities($var, ENT_QUOTES, "UTF-8");
		else if (!empty($field['default'])) {
			$field['value'] = $field['default'];
			unset($field['default']);
		}
		$control = "input";
	}
}
?>
