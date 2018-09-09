<?php
namespace Starbug\Core;

class FormTagSelectHook extends FormHook {
  function build($form, &$control, &$field) {
    $value = $form->get($field['name']);
    if ((empty($value)) && (!empty($field['default']))) {
      $form->set($field['name'], $field['default']);
      unset($field['default']);
    }
    if (!is_array($value)) $value = explode(",", $value);
    foreach ($value as $idx => $v) {
      if (substr($v, 0, 1) == "-") unset($value[$idx]);
    }
    $form->assign("value", $value);
  }
}
