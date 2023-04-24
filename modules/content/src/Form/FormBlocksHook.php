<?php
namespace Starbug\Content\Form;

use Starbug\Core\FormHook;

class FormBlocksHook extends FormHook {
  public function build($form, &$control, &$field) {
    $value = $form->get($field['name']);
    if ((empty($value)) && (!empty($field['default']))) {
      $form->set($field['name'], $field['default']);
      $value = $field['default'];
      unset($field['default']);
    }
    if (empty($value)) {
      $value = ["content-1" => ""];
    }
    $field["value"] = $value;
    $field['nolabel'] = true;
    $field['class'] = "rich-text";
    $field['style'] = "width:100%;height:100px";
  }
}
