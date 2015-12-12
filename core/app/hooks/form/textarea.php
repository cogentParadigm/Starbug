<?php
namespace Starbug\Core;
class hook_form_textarea extends FormHook {
	function build($form, &$control, &$field) {
		if (empty($field['cols'])) $field['cols'] = "35";
		if (empty($field['rows'])) $field['rows'] = "8";
		//POSTed or default value
		$value = $form->get($field['name']);
		if (!empty($field['default'])) {
			if (empty($value)) $value = $field['default'];
			unset($field['default']);
		}
		$form->assign("value", $form->set($field['name'], htmlentities($value, ENT_QUOTES, "UTF-8")));
	}
}
?>
