<?php
namespace Starbug\ShippingMethods\Admin;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\DisplayFactoryInterface;
use Starbug\Core\FormDisplay;
use Starbug\Core\FormHookFactoryInterface;
use Starbug\Db\CollectionFactoryInterface;
use Starbug\Db\DatabaseInterface;
use Starbug\Templates\TemplateInterface;

class ShippingRatesProductOptionsForm extends FormDisplay {
  public $model = "shipping_rates_product_options";
  public function __construct(
    TemplateInterface $output,
    CollectionFactoryInterface $collections,
    FormHookFactoryInterface $hookFactory,
    DisplayFactoryInterface $displays,
    ServerRequestInterface $request,
    DatabaseInterface $db
  ) {
    parent::__construct($output, $collections, $hookFactory, $displays, $request);
    $this->db = $db;
  }
  public function buildDisplay($options) {
    if ($this->success("create") && !$this->hasPost("id")) {
      $this->setPost("id", $this->db->getInsertId($this->model));
    }
    $tree = $this->getOptionsTree();
    $this->add(["product_options_id", "label" => "Product Option", "input_type" => "select", "div" => "col-sm-4"] + $tree);
    $this->add([
      "operator",
      "input_type" => "select",
      "div" => "col-sm-3 col-md-2",
      "options" => ["is equal to", "is not equal to", "is empty", "is not empty"],
      "data-dojo-type" => "starbug/form/Dependency",
      "data-dojo-props" => "key:'operator'"
    ]);
    $this->add(["value", "input_type" => "textarea", "div" => "col-sm-4", "data-dojo-type" => "starbug/form/Dependent", "data-dojo-props" => "key:'operator', values:['is equal to', 'is not equal to']"]);
  }
  public function getOptionsTree($parent = 0, $prefix = "") {
    $options = $values = [""];
    $query = $this->db->query("product_options")->conditions(["parent" => $parent]);
    $items = $query->all();
    foreach ($items as $item) {
      if (!empty($prefix)) {
        $item['name'] = $prefix.$item['name'];
      }
      $values[] = $item['id'];
      $options[] = $item['name'];
      $results = $this->getOptionsTree($item['id'], $item['name'].': ');
      $values = array_merge($values, $results['values']);
      $options = array_merge($options, $results['options']);
    }
    return ['options' => $options, 'values' => $values];
  }
}
