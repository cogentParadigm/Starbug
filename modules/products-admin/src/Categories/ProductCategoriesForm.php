<?php
namespace Starbug\Products\Admin\Categories;

use Starbug\Core\FormDisplay;

class ProductCategoriesForm extends FormDisplay {
  public $model = "product_categories";
  public $cancel_url = "admin/product-categories";
  public $collection = ProductCategoriesFormCollection::class;
  public function buildDisplay($options) {
    $options += ["tab" => ""];
    // layout
    $this->layout->add(["top", "left" => "div.col-12"]);
    $this->layout->add(["bottom", "tabs" => "div.col-12"]);
    $this->layout->put("tabs", 'div[data-dojo-type="dijit/layout/TabContainer"][data-dojo-props="doLayout:false, tabPosition:\'left-h\'"][style="width:100%;height:100%"]', '', 'tc');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="URL path"]'.((empty($options['tab'])) ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'path');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Parent"]'.(($options['tab'] === "breadcrumbs") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'breadcrumbs');
    // left
    $this->add(["name", "pane" => "left"]);
    $this->add(["description", "pane" => "left", "input_type" => "textarea", "class" => "rich-text"]);
    // right
    $this->add(["path", "label" => "URL path", "info" => "Leave empty to generate automatically", "input_type" => "text", "pane" => "path"]);
    $this->add([
      "parent",
      "data-dojo-type" => "sb/form/Select",
      "data-dojo-props" => "model: 'product_categories', searchable: true",
      "pane" => "breadcrumbs"
    ]);
  }
}
