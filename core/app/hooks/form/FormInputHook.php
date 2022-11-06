<?php
namespace Starbug\Core;

class FormInputHook extends FormHook {
  public function build($form, &$control, &$field) {
    $var = $form->get($field['name']);
    if (!empty($var)) {
      $field['value'] = htmlentities($var, ENT_QUOTES, "UTF-8");
    } elseif (!empty($field['default'])) {
      $field['value'] = $field['default'];
      unset($field['default']);
    }
  }
}
