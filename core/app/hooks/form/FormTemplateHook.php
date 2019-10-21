<?php
namespace Starbug\Core;

class FormTemplateHook extends FormHook {
  public function build($form, &$control, &$field) {
    $field['nofield'] = true;
  }
}
