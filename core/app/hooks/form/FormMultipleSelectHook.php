<?php
namespace Starbug\Core;

class FormMultipleSelectHook extends FormHook {
  public function build($form, &$control, &$field) {
    $value = $form->get($field['name']);
    if ((empty($value)) && (!empty($field['default']))) {
      $form->set($field['name'], $field['default']);
      unset($field['default']);
    }
    if (!is_array($value)) {
      $value = explode(",", $value);
    }
    foreach ($value as $idx => $v) {
      if (empty($v) || substr($v, 0, 1) == "-") {
        unset($value[$idx]);
      }
    }
    if (empty($field["displayType"])) {
      $field["displayType"] = "CheckboxDisplay";
    }

    if (!empty($field["from"]) && empty($field["query"])) {
      $field["query"] = "Select";
    }

    $other_option = empty($field['other_option']) ? false : $field['other_option'];
    $form->assign("value", $value);
    $form->assign("options", $field["options"] ?? []);
    $form->assign("other_option", $other_option);
  }
}
