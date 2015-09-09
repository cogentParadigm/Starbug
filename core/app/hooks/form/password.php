<?php
namespace Starbug\Core;
class hook_form_password extends FormHook {
	function build($form, &$control, &$field) {
		$field['type'] = 'password';
		$control = "input";
	}
}
?>
