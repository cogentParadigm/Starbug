<?php
namespace Starbug\Core;

class FormHtmlHook extends FormHook {
  public function build($form, &$control, &$field) {
    $field['nofield'] = true;
  }
}
