<?php
namespace Starbug\Core;
class FormFileSelectHook extends FormHook {
	function build($form, &$control, &$field) {
		$field['type'] = 'file';
		$field['value'] = $form->get($field['name']);
		$control = "input";
	}
}
?>
