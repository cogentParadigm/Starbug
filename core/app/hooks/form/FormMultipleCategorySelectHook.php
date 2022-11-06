<?php
namespace Starbug\Core;

class FormMultipleCategorySelectHook extends FormHook {
  public function __construct(CollectionFactoryInterface $collections) {
    $this->collections = $collections;
  }
  public function build($form, &$control, &$field) {
    $value = $form->get($field['name']);
    if ((empty($value)) && (!empty($field['default']))) {
      $form->set($field['name'], $field['default']);
      unset($field['default']);
    }
    if (empty($field['taxonomy'])) {
      $field['taxonomy'] = ((empty($form->model)) ? "" : $form->model."_").$field['name'];
    }
    if (empty($field['parent'])) {
      $field['parent'] = 0;
    }
    if (empty($field["query"])) {
      $field["query"] = [];
    }
    $terms = $this->collections->get(TermsTreeCollection::class)->query($field["query"] + ["taxonomy" => $field['taxonomy'], "parent" => $field['parent']]);
    $value = $form->get($field['name']);
    if (!is_array($value)) {
      $value = explode(",", $value);
    }
    foreach ($value as $idx => $v) {
      if (substr($v, 0, 1) == "-") {
        unset($value[$idx]);
      }
    }
    $field['value'] = $value;
    $field['terms'] = $terms;
    $field['writable'] = isset($field['writable']);
  }
}
