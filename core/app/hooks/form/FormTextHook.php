<?php
namespace Starbug\Core;

class FormTextHook extends FormHook {
  public function build($form, &$control, &$field) {
    $field['type'] = 'text';
    // POSTed or default value
    $var = $form->get($field['name']);
    if ($var != "") {
      $field['value'] = htmlentities($var, ENT_QUOTES, "UTF-8");
    } elseif (isset($field['default'])) {
      $field['value'] = $field['default'];
      unset($field['default']);
    }
    $control = "input";
  }
}
