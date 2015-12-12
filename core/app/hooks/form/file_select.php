<?php
namespace Starbug\Core;
class hook_form_file_select extends FormHook {
	function build($form, &$control, &$field) {
		$field['type'] = 'file';
		$field['value'] = $form->get($field['name']);
		$control = "input";
	}
}
?>
