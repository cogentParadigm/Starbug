<?php
namespace Starbug\Core;

class FormTemplateHook extends FormHook {
  function build($form, &$control, &$field) {
    $field['nofield'] = true;
  }
}
