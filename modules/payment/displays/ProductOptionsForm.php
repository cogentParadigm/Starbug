<?php

namespace Starbug\Payment;

use Starbug\Core\FormDisplay;
use Starbug\Db\Schema\SchemerInterface;

class ProductOptionsForm extends FormDisplay {
  public $model = "product_options";
  public $cancel_url = "admin/product_options";
  public function setTableSchema(SchemerInterface $schemer) {
    $this->tableSchema = $schemer->getSchema();
  }
  function build_display($options) {
    $tree = $this->getOptionsTree($options["product_types_id"]);
    $this->add("name");
    $this->add("slug");
    $this->add(["description", "input_type" => "textarea"]);
    $this->add(["type", "input_type" => "select", "options" => "Fieldset,Select List,Text,Value,File,Reference", "data-dojo-type" => "starbug/form/Dependency", "data-dojo-props" => "key:'type'"]);
    $this->add(["reference_type", "input_type" => "select", "options" => $this->getReferenceTypes(), "data-dojo-type" => "starbug/form/Dependent", "data-dojo-props" => "key:'type',values:['Reference']"]);
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
  public function getReferenceTypes() {
    return array_merge([""], array_keys($this->tableSchema->getTables()));
  }
}
