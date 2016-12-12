<?php
namespace Starbug\Core;
class hook_form_category_select extends FormHook {
	protected $taxonomy;
	function __construct(TaxonomyInterface $taxonomy) {
		$this->taxonomy = $taxonomy;
	}
	function build($form, &$control, &$field) {
		$value_name = $field['name'];
		if (isset($field['multiple'])) {
			$field['multiple'] = "multiple";
			$field['name'] = $field['name']."[]";
			if (empty($field['size'])) $field['size'] = 5;
		}
		$value = $form->get($value_name);
		if ((empty($value)) && (!empty($field['default']))) {
			$form->set($field['name'], $field['default']);
			$value = $field["default"];
			unset($field['default']);
		}
		if (empty($field['taxonomy'])) $field['taxonomy'] = ((empty($form->model)) ? "" : $form->model."_").$field['name'];
		if (empty($field['parent'])) $field['parent'] = 0;
		$terms = $this->taxonomy->terms($field['taxonomy'], $field['parent']);
		$options = array();
		foreach ($terms as $term) $options[str_pad($term['term'], strlen($term['term'])+$term['depth'], "-", STR_PAD_LEFT)] = $term['slug'];
		if (isset($field['optional'])) array_unshift($terms, array("term" => $field['optional'], "id" => 0));
		$field['value'] = $form->get($value_name);
		$form->assign("options", $options);
		$field["terms"] = $terms;
		$field["value"] = $value;
	}
}
?>
