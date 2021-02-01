<?php
namespace Starbug\Core;

class FormCheckboxHook extends FormHook {
  public function build($form, &$control, &$field) {
    $field['type'] = 'checkbox';
    $value = $form->get($field['name']);
    if (($value === '' && $field['value'] == ($field['default'] ?? "")) || $value == $field['value']) $field['checked'] = 'checked';
    if (!isset($field["unchecked"])) {
      $field["unchecked"] = 0;
    }
    $control = 'input';
  }
}
