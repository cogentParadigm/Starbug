<?php
namespace Starbug\Core;

class FormRadioSelectHook extends FormHook {
  public function build($form, &$control, &$field) {
    $value = $form->get($field["name"]);
    if ((empty($value)) && (!empty($field["default"]))) {
      $form->set($field["name"], $field["default"]);
      $value = $field["default"];
      unset($field['default']);
    }

    if (!empty($field["from"]) && empty($field["query"])) {
      $field["query"] = "Select";
    }

    $other_option = empty($field['other_option']) ? false : $field['other_option'];
    $display_options = empty($field['display_options']) ? [] : $field['display_options'];
    $form->assign("value", $value);
    $form->assign("other_option", $other_option);
    $form->assign("display_options", $display_options);
  }
}
