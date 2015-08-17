<?php
class hook_form_html extends FormHook {
	function build($form, &$control, &$field) {
		$field['nofield'] = true;
	}
}
?>
