<?php
namespace Starbug\Core;
class hook_form_template extends FormHook {
	function build($form, &$control, &$field) {
		$field['nofield'] = true;
	}
}
?>
