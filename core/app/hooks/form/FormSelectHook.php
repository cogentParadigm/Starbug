<?php
namespace Starbug\Core;

use Invoker\InvokerInterface;

class FormSelectHook extends FormHook {
  public function __construct(
    protected InvokerInterface $invoker
  ) {
  }
  public function build($form, &$control, &$field) {
    $name = $field['name'];
    $value = $form->get($field['name']);
    if ((empty($value)) && (!empty($field['default']))) {
      $form->set($field['name'], $field['default']);
      $value = $field['default'];
      unset($field['default']);
    }
    if (isset($field['multiple'])) {
      $field['multiple'] = "multiple";
      if (empty($field['size'])) {
        $field['size'] = 5;
      }
      if (!is_array($value)) {
        $value = explode(',', $value);
      }
    }
    if (!empty($field['range'])) {
      $range = explode("-", $field['range']);
      for ($i = $range[0]; $i <= $range[1]; $i++) {
        $options[$i] = $i;
      }
      unset($field['range']);
    }
    if (!empty($field["resolve_options"])) {
      $field["options"] = $this->invoker->call($field["resolve_options"], [$field]);
    }
    if (!empty($field['options'])) {
      $options = is_array($field['options']) ? $field['options'] : explode(",", $field['options']);
      if (isset($options[0])) {
        $keys = $options;
        $values = (!empty($field['values'])) ? $field['values'] : $keys;
        $values = (is_array($values)) ? $values : explode(",", $field['values']);
        $options = [];
        foreach ($keys as $i => $k) {
          $options[] = ["id" => $values[$i], "label" => $k];
        }
      }
      unset($field['options']);
      unset($field['values']);
    } else {
      if (!empty($field["from"]) && empty($field["query"])) {
        $field["query"] = "Select";
      }
    }
    $optional = false;
    if (isset($field['optional'])) {
      $optional = $field['optional'];
    }
    $other_option = empty($field['other_option']) ? false : $field['other_option'];
    $form->assign("optional", $optional);
    $form->assign("value", $value);
    $form->assign("options", $options ?? []);
    $form->assign("other_option", $other_option);
  }
}
