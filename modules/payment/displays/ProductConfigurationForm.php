<?php
namespace Starbug\Payment;

use Starbug\Core\FormDisplay;

class ProductConfigurationForm extends FormDisplay {
  public $model = "products";
  public $default_action = "add";
  public $submit_label = "Add To Cart";
  public function build_display($ops) {
    $options = $this->db->query("product_options")->condition("product_types_id", $ops["type"])
      ->sort("product_options.tree_path, product_options.position")->all();
    $items = $children = [];
    foreach ($options as $option) {
      if (empty($option["parent"])) {
        $items[] = $option;
      } else {
        $children[$option["parent"]][] = $option;
      }
    }
    $this->addOptions($items, $children);
  }
  protected function addOptions($items, $children) {
    foreach ($items as $item) {
      $this->layout->add([$item["id"]."Row", $item["id"]."Cell" => "div.col-xs-12"]);
      if ($item["type"] == "Fieldset") {
        $this->layout->put($item["id"]."Cell", "div", "", $item["id"]."Fieldset");
        if (!empty($children[$item["id"]])) {
          $this->addOptions($children[$item["id"]], $children);
        }
      } else if ($item["type"] == "Text") {
        $target = empty($item["parent"]) ? $item["id"] : $item["parent"]."Fieldset";
        $this->add(["options[".$item["id"]."]", "input_type" => "text", "label" => $item["name"], "pane" => $target]);
      }
    }
  }
}