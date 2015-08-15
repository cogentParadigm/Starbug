<?php
class hook_form_radio_select extends FormHook {
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
		$form->assign("value", $value);
		$form->assign("other_option", $other_option);
	}
}
?>
