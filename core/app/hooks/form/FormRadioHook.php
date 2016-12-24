<?php
namespace Starbug\Core;
class FormRadioHook extends FormHook {
	function build($form, &$control, &$field) {
		$field['type'] = 'radio';
		if ($form->get($field['name']) == $field['value']) $field['checked'] = 'checked';
		$control = "input";
	}
}
?>
