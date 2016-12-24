<?php
namespace Starbug\Core;
class FormMultipleCategorySelectHook extends FormHook {
	protected $taxonomy;
	function __construct(TaxonomyInterface $taxonomy) {
		$this->taxonomy = $taxonomy;
	}
	function build($form, &$control, &$field) {
		$value = $form->get($field['name']);
		if ((empty($value)) && (!empty($field['default']))) {
			$form->set($field['name'], $field['default']);
			unset($field['default']);
		}
		if (empty($field['taxonomy'])) $field['taxonomy'] = ((empty($form->model)) ? "" : $form->model."_").$field['name'];
		if (empty($field['parent'])) $field['parent'] = 0;
		$terms = $this->taxonomy->terms($field['taxonomy'], $field['parent']);
		$value = $form->get($field['name']);
		if (!is_array($value)) $value = explode(",",$value);
		foreach ($value as $idx => $v) {
			if (substr($v, 0, 1) == "-") unset($value[$idx]);
		}
		$field['value'] = $value;
		$field['terms'] = $terms;
		$field['writable'] = isset($field['writable']);
	}
}
