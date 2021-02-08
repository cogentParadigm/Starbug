<?php
namespace Starbug\Intl;

use Starbug\Core\FormHook;
use Starbug\Core\DatabaseInterface;

class FormAddressHook extends FormHook {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function build($form, &$control, &$field) {
    $options = ["code" => "US", "input_name" => array_merge($form->input_name, [$field["name"]])];
    unset($field["class"]);
    $value = $form->get($field['name']);
    if ((empty($value)) && (!empty($field['default']))) {
      $value = $field["default"];
      unset($field['default']);
    }
    if (empty($field['data-dojo-type'])) $field['data-dojo-type'] = 'starbug/form/Address';
    if (!isset($field['data-dojo-props']) || !is_array($field['data-dojo-props'])) {
      $field['data-dojo-props'] = [];
    }
    $field['data-dojo-props']['updateOnLoad'] = "false";
    $field['data-dojo-props']['keys'] = "['".implode("', '", $form->input_name)."', '".$field["name"]."']";
    if (!empty($value) && $value !== "NULL") {
      if (is_array($value)) {
        if (!empty($value["id"])) {
          $field['data-dojo-props']['id'] = $value["id"];
          $options["id"] = $value["id"];
        }
        $country = $this->db->query("countries")->condition("id", $value["country"])->one();
        $options["code"] = $country["code"];
      } else {
        $field['data-dojo-props']['id'] = $value;
        $address = $this->db->query("address")->condition("address.id", $value)->select("country.code")->one();
        $options["code"] = $address["code"];
        $options["id"] = $value;
      }
    }
    $props = [];
    foreach ($field['data-dojo-props'] as $k => $v) {
      $props[] = $k.':'.$v;
    }
    $field['data-dojo-props'] = implode(', ', $props);
    $field["options"] = $options;
  }
}
