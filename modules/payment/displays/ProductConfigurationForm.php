<?php
namespace Starbug\Payment;

use Starbug\Core\FormDisplay;

class ProductConfigurationForm extends FormDisplay {
  public $model = "products";
  public $default_action = "add";
  public $submit_label = "Add To Cart";
  public $collection = "ProductConfigurationForm";
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
    $this->layout->add(["row", "container" => "div.col-xs-12"]);
    $this->addOptions($items, $children);
  }
  protected function addOptions($items, $children) {
    foreach ($items as $item) {
      $input_name = "options[".$item["slug"]."]";
      $target = empty($item["parent"]) ? "container" : $item["parent"];
      $field = [$input_name, "label" => $item["name"], "pane" => $target, "div" => "col-xs-12 col-sm-".$item["columns"], "required" => (bool) $item["required"]];
      if ($item["type"] == "Fieldset") {
        $this->layout->put($target, "div.col-xs-12.col-sm-".$item["columns"], "", $item["id"]."FieldsetCol");
        $this->layout->put($item["id"]."FieldsetCol", "div.panel.panel-default", "", $item["id"]."FieldsetPanel");
        $this->layout->put($item["id"]."FieldsetPanel", "div.panel-heading", $item["name"]);
        $this->layout->put($item["id"]."FieldsetPanel", "div.panel-body", "", $item["id"]."PanelBody");
        $this->layout->put($item["id"]."PanelBody", "div.row", "", $item["id"]);
        if (!empty($children[$item["id"]])) {
          $this->addOptions($children[$item["id"]], $children);
        }
      } elseif ($item["type"] == "Text") {
        $this->add($field + ["input_type" => "text"]);
      } elseif ($item["type"] == "Textarea") {
        $this->add($field + ["input_type" => "textarea"]);
      } elseif ($item["type"] == "Select List") {
        $options = $values = [""];
        if (!empty($children[$item["id"]])) {
          foreach ($children[$item["id"]] as $option) {
            $options[] = $option["name"];
            $values[] = $option["slug"];
          }
        }
        $this->add($field + ["input_type" => "select", "options" => $options, "values" => $values]);
      } elseif ($item["type"] == "Reference") {
        $this->add($field + ["input_type" => "text", "data-dojo-type" => "sb/form/Select", "data-dojo-props" => "model:'".$item["reference_type"]."'"]);
      } elseif ($item["type"] == "Hidden") {
        $this->add($field + ["input_type" => "hidden"]);
      } elseif ($item["type"] == "File") {
        $this->add($field + ["input_type" => "file_select"]);
      }
    }
  }
}
