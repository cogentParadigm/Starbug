<?php
namespace Starbug\Core;

class FormAutocompleteHook extends FormHook {
  public function build($form, &$control, &$field) {
    $field['type'] = 'text';
    $field['autocomplete'] = "off";
    $field['data-dojo-type'] = "starbug/form/Autocomplete";
    if (!isset($field['data-dojo-props'])) {
      $query_action = empty($field['action']) ? "select" : $field['action'];
      $query_model = empty($field['from']) ? $this->model : $field['from'];
      $query = empty($field['query']) ? "{}" : $field['query'];
      $field['data-dojo-props'] = [];
      $field['data-dojo-props'][] = "store:sb.get('".$query_model."', '".$query_action."')";
      $field['data-dojo-props'][] = "query:".$query;
      $field['data-dojo-props'] = implode(", ", $field['data-dojo-props']);
    }
    $field['div'] = (empty($field['div']) ? "" : $field['div']." ")."autocomplete";
    // POSTed or default value
    $var = $form->get($field['name']);
    if (!empty($var)) $field['value'] = htmlentities($var, ENT_QUOTES, "UTF-8");
    elseif (!empty($field['default'])) {
      $field['value'] = $field['default'];
      unset($field['default']);
    }
    $control = "input";
  }
}
