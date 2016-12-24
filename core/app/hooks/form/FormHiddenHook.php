<?php
namespace Starbug\Core;
class FormHiddenHook extends FormHook {
	function build($form, &$control, &$field) {
		$field['type'] = 'hidden';
		$field['nolabel'] = $field['nodiv'] = true;
		//POSTed or default value
		$var = $form->get($field['name']);
		if (!empty($var)) $field['value'] = htmlentities($var, ENT_QUOTES, "UTF-8");
		else if (!empty($field['default'])) {
			$field['value'] = $field['default'];
			unset($field['default']);
		}
		$control = "input";
	}
}
