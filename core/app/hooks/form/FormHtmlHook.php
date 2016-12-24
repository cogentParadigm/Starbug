<?php
namespace Starbug\Core;
class FormHtmlHook extends FormHook {
	function build($form, &$control, &$field) {
		$field['nofield'] = true;
	}
}
