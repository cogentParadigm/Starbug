<?php
namespace Starbug\Core;

class FormSelectHook extends FormHook {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  function build($form, &$control, &$field) {
    $name = $field['name'];
    $value = $form->get($field['name']);
    if ((empty($value)) && (!empty($field['default']))) {
      $form->set($field['name'], $field['default']);
      $value = $field['default'];
      unset($field['default']);
    }
    if (isset($field['multiple'])) {
      $field['multiple'] = "multiple";
      if (empty($field['size'])) $field['size'] = 5;
      if (!is_array($value)) $value = explode(',', $value);
    }
    if (!empty($field['range'])) {
      $range = explode("-", $field['range']);
      for ($i = $range[0]; $i <= $range[1]; $i++) {
        $options[$i] = $i;
      }
      unset($field['range']);
    }
    $mode = "template";
    if (!empty($field['caption'])) {
      if (!empty($field['from'])) {
        $list = $options;
        $options = $this->db->query($field['from'], $field)->all();
      } else $list = array();
      $keys = array();
      if (!empty($options)) foreach ($options[0] as $k => $v) if (false !== strpos($field['caption'], "%$k%")) $keys[] = $k;
      foreach ($options as $o) {
        $cap = $field['caption'];
        foreach ($keys as $k) $cap = str_replace("%$k%", $o[$k], $cap);
        $list[$cap] = $o[$field['value']];
      }
      $options = $list;
      unset($field['caption']);
      unset($field['value']);
    } else if (!empty($field['options'])) {
      $keys = is_array($field['options']) ? $field['options'] : explode(",", $field['options']);
      $values = (!empty($field['values'])) ? $field['values'] : $keys;
      $values = (is_array($values)) ? $values : explode(",", $field['values']);
      $options = array();
      foreach ($keys as $i => $k) $options[$k] = $values[$i];
      unset($field['options']);
      unset($field['values']);
    } else {
      $info = $form->schema[$field['name']];
      if (!empty($info['references'])) {
        if (empty($field['from'])) $field['from'] = reset(explode(" ", $info['references']));
        if (empty($field['query'])) $field['query'] = "Select";
      }
      if (!empty($field['query']) && !empty($field['from'])) {
        $mode = "display";
      }
    }
    $optional = false;
    if (isset($field['optional'])) $optional = $field['optional'];
    $other_option = empty($field['other_option']) ? false : $field['other_option'];
    $form->assign("optional", $optional);
    $form->assign("value", $value);
    $form->assign("options", $options);
    $form->assign("mode", $mode);
    $form->assign("other_option", $other_option);
  }
}
