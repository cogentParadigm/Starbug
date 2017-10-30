<?php
namespace Starbug\Core;
class FormRadioSelectHook extends FormHook {
	function __construct(ModelFactoryInterface $models) {
		$this->models = $models;
	}
	function build($form, &$control, &$field) {
		$value = $form->get($field['name']);
		if ((empty($value)) && (!empty($field['default']))) {
			$form->set($field['name'], $field['default']);
			unset($field['default']);
		}

		$info = $form->schema[$field['name']];
		if ($this->models->has($info['type'])) {
			if (empty($field['from'])) $field['from'] = $info['type'];
			if (empty($field['query'])) $field['query'] = "select";
		}
		$other_option = empty($field['other_option']) ? false : $field['other_option'];
		$display_options = empty($field['display_options']) ? [] : $field['display_options'];
		$form->assign("value", $value);
		$form->assign("other_option", $other_option);
		$form->assign("display_options", $display_options);
	}
}
