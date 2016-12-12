<?php
namespace Starbug\Core;
class hook_form_crud extends FormHook {
	public function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	function build($form, &$control, &$field) {
		$var = $form->get($field['name']);
		if (!empty($var)) {
			if (is_array($var)) {
				foreach ($var as $idx => $v) if (substr($v, 0, 1) !== "-") $var[$idx] = htmlentities($v, ENT_QUOTES, "UTF-8");
				$field['value'] = $var;
			} else {
				$field['value'] = htmlentities($var, ENT_QUOTES, "UTF-8");
			}
		}
		else if (!empty($field['default'])) {
			$field['value'] = $field['default'];
			unset($field['default']);
		}
		unset($field['class']);
		if (empty($field['data-dojo-type'])) $field['data-dojo-type'] = 'starbug/form/CRUDSelect';
		if (!is_array($field['data-dojo-props'])) {
			$field['data-dojo-props'] = array();
		}
		$field['data-dojo-props']['input_name'] = "'".$form->get_name($field['name'])."'";
		$field['data-dojo-props']['model'] = "'".$field['table']."'";
		$field['data-dojo-props']['value'] = '[]';
		if (!empty($field['size'])) $field['data-dojo-props']['size'] = $field['size'];
		if (!empty($field['value'])) {
			$value = is_array($field['value']) ? implode(",", $field['value']) : $field['value'];
			$field['data-dojo-props']['value'] = '['.str_replace('#', '', $value).']';
		}
		$props = array();
		foreach ($field['data-dojo-props'] as $k => $v) {
			$props[] = $k.':'.$v;
		}
		$field['data-dojo-props'] = implode(', ', $props);
	}
}
?>
