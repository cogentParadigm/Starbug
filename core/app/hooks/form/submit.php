<?php
namespace Starbug\Core;
class hook_form_submit extends FormHook {
	function build($form, &$control, &$field) {
		$field['type'] = 'submit';
		$field['nolabel'] = true;
		$control = "input";
	}
}
?>
