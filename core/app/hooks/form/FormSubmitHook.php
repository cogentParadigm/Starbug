<?php
namespace Starbug\Core;

class FormSubmitHook extends FormHook {
  function build($form, &$control, &$field) {
    $field['type'] = 'submit';
    $field['nolabel'] = true;
    $control = "input";
  }
}
