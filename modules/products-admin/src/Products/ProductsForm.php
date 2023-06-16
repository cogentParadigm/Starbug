<?php
namespace Starbug\Products\Admin\Products;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\DisplayFactoryInterface;
use Starbug\Db\DatabaseInterface;
use Starbug\Core\FormDisplay;
use Starbug\Core\FormHookFactoryInterface;
use Starbug\Db\CollectionFactoryInterface;
use Starbug\Templates\TemplateInterface;

class ProductsForm extends FormDisplay {
  public $model = "products";
  public $cancel_url = "admin/products";
  public $collection = ProductsFormCollection::class;
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
    $options += ["tab" => ""];
    $this->layout->add(["main", "right" => "div.col-3.order-last", "left" => "div.col-9"]);
    $this->layout->put("left", "div", "", "top");
    $this->layout->put("left", "div", "", "center");
    $this->layout->put("left", "div", "", "bottom");
    $this->layout->add(["footer", "tabs" => "div.col-12"]);
    $this->layout->put("tabs", 'div[data-dojo-type="dijit/layout/TabContainer"][data-dojo-props="doLayout:false, tabPosition:\'left-h\'"][style="width:100%;height:100%"]', '', 'tc');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="URL path"]'.((empty($options['tab'])) ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'path');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Meta tags"]'.(($options['tab'] === "meta") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'meta');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Publishing options"]'.(($options['tab'] === "breadcrumbs") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'publishing');
    $this->add(["type", "input_type" => "select", "from" => "product_types", "optional" => "", "pane" => "top", "data-submit" => "change"]);
    $this->add(["sku", "label" => "SKU", "pane" => "top"]);
    $this->add(["name", "pane" => "top"]);
    $this->add(["description", "input_type" => "textarea", "pane" => "bottom"]);
    $this->add(["content", "input_type" => "textarea", "class" => "rich-text", "pane" => "bottom"]);
    $this->add(["thumbnail", "input_type" => "text", "pane" => "bottom", "data-dojo-type" => "sb/form/FileList", "data-dojo-props" => "browseEnabled: true"]);
    $this->add(["photos", "input_type" => "text", "pane" => "bottom", "data-dojo-type" => "sb/form/FileList", "data-dojo-props" => "selectionParams: {size: 0}, browseEnabled: true"]);

    $this->add([
      "payment_type",
      "pane" => "right",
      "input_type" => "select",
      "options" => ["Single Payment", "Recurring Payments"],
      "values" => ["single", "recurring"],
      "data-dojo-type" => "starbug/form/Dependency",
      "data-dojo-props" => "key:'payment_type'"
    ]);
    $this->add(["price", "pane" => "right", "data-dojo-type" => "sb/form/Currency"]);
    $this->add([
      "unit",
      "label" => "Recurrence Unit",
      "pane" => "right",
      "input_type" => "select",
      "options" => ["Days", "Weeks", "Months", "Years"],
      "values" => ["days", "weeks", "months", "years"],
      "data-dojo-type" => "starbug/form/Dependent",
      "data-dojo-props" => "key:'payment_type', values:['recurring']"
    ]);
    $this->add([
      "interval",
      "label" => "Recurrence Interval",
      "pane" => "right",
      "info" => "For example, enter 6 and select a recurrence unit of 'Months' to bill every 6 months.",
      "data-dojo-type" => "starbug/form/Dependent",
      "data-dojo-props" => "key:'payment_type', values:['recurring']"
    ]);
    $this->add([
      "limit",
      "label" => "Total Payments",
      "pane" => "right",
      "info" => "Enter a number to limit the total number of payments.",
      "data-dojo-type" => "starbug/form/Dependent",
      "data-dojo-props" => "key:'payment_type', values:['recurring']"
    ]);

    $this->add(["categories", "input_type" => "multiple_select", "from" => "product_categories", "pane" => "right"]);
    $this->add(["tags", "input_type" => "text", "data-dojo-type" => "sb/form/MultipleSelect", "data-dojo-props" => "model:'product_tags'", "pane" => "right"]);
    $this->add(["position", "pane" => "right"]);
    $this->add(["path", "label" => "URL path", "info" => "Leave empty to generate automatically", "pane" => "path"]);
    $this->add(["meta_description", "label" => "Meta Description", "input_type" => "textarea", "class" => "plain", "style" => "width:100%", "data-dojo-type" => "dijit/form/Textarea", "pane" => "meta"]);
    $this->add(["meta_keywords", "label" => "Meta Keywords", "input_type" => "textarea", "class" => "plain", "style" => "width:100%", "data-dojo-type" => "dijit/form/Textarea", "pane" => "meta"]);
    $this->add(["published", "pane" => "publishing", "input_type" => "checkbox", "value" => "1"]);
    $this->add(["hidden", "pane" => "publishing", "input_type" => "checkbox", "value" => "1"]);

    $options = $this->db->query("product_options")->condition("product_types_id", $this->get("type"))
      ->sort("product_options.tree_path, product_options.position")->all();
    $items = $children = [];
    foreach ($options as $option) {
      if (empty($option["parent"])) {
        $items[] = $option;
      } else {
        $children[$option["parent"]][] = $option;
      }
    }
    $this->layout->put("center", "div.row", "", "container");
    $this->addOptions($items, $children);
    $this->actions->update([$this->defaultAction, "data-submit" => "finish"]);
  }
  protected function addOptions($items, $children) {
    foreach ($items as $item) {
      $input_name = "options[".$item["slug"]."]";
      $target = empty($item["parent"]) ? "container" : $item["parent"];
      $field = [$input_name, "label" => $item["name"], "pane" => $target, "div" => "col-".$item["columns"], "required" => (bool) $item["required"]];
      if ($item["type"] == "Fieldset") {
        $this->layout->put($target, "div.col-".$item["columns"], "", $item["id"]."FieldsetCol");
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
      } elseif ($item["type"] == "Checkbox") {
        $this->add($field + ["input_type" => "checkbox", "value" => "1"]);
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
        $this->add($field + ["input_type" => "text", "data-dojo-type" => "sb/form/Uploader"]);
      }
    }
  }
}
