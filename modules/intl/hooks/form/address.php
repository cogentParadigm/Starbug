<?php
namespace Starbug\Intl;
use Starbug\Core\FormHook;
class hook_form_address extends FormHook {
	function build($form, &$control, &$field) {
		unset($field["class"]);
		$value = $form->get($field['name']);
		if ((empty($value)) && (!empty($field['default']))) {
			$value = $field["default"];
			unset($field['default']);
		}
		if (empty($field['data-dojo-type'])) $field['data-dojo-type'] = 'starbug/form/Address';
		if (!is_array($field['data-dojo-props'])) {
			$field['data-dojo-props'] = array();
		}
		$field['data-dojo-props']['keys'] = "['".implode("', '", $form->input_name)."', '".$field["name"]."']";
		if (!empty($value)) {
			$field['data-dojo-props']['id'] = $value;
		}
		$props = array();
		foreach ($field['data-dojo-props'] as $k => $v) {
			$props[] = $k.':'.$v;
		}
		$field['data-dojo-props'] = implode(', ', $props);
	}
}
?>
