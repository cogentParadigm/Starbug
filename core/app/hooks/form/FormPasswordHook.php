<?php
namespace Starbug\Core;

class FormPasswordHook extends FormHook {
  function build($form, &$control, &$field) {
    $field['type'] = 'password';
    $control = "input";
  }
}
