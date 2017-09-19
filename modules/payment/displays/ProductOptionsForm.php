<?php

namespace Starbug\Payment;

use Starbug\Core\FormDisplay;

class ProductOptionsForm extends FormDisplay {
  public $model = "product_options";
  public $cancel_url = "admin/product_options";
  function build_display($options) {
    $tree = $this->getOptionsTree($options["product_types_id"]);
    $this->add("name");
    $this->add("slug");
    $this->add(["description", "input_type" => "textarea"]);
    $this->add(["type", "input_type" => "select", "options" => "Fieldset,Select List,Text,Value,File"]);
    $this->add("required");
    $this->add(["parent", "input_type" => "select"] + $tree);
    $this->add("position");
  }
  public function getOptionsTree($type, $parent = 0, $prefix = "") {
    $options = $values = [""];
    $items = $this->db->query("product_options")->conditions(["product_types_id" => $type, "parent" => $parent])->all();
    foreach ($items as $item) {
      if (!empty($prefix)) $item['name'] = $prefix.$item['name'];
      $values[] = $item['id'];
      $options[] = $item['name'];
      $results = $this->getOptionsTree($type, $item['id'], $item['name'].': ');
      $values = array_merge($values, $results['values']);
      $options = array_merge($options, $results['options']);
    }
    return ['options' => $options, 'values' => $values];
  }
}
