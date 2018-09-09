<?php
namespace Starbug\Core;

class FormCheckboxHook extends FormHook {
  function build($form, &$control, &$field) {
    $field['type'] = 'checkbox';
    $value = $form->get($field['name']);
    if (($value === '' && $field['value'] == $field['default']) || $value == $field['value']) $field['checked'] = 'checked';
    $control = 'input';
  }
}
