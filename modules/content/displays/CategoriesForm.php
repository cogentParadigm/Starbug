<?php
namespace Starbug\Content;

use Starbug\Core\FormDisplay;

class CategoriesForm extends FormDisplay {
  public $model = "categories";
  public $cancel_url = "admin/categories";
  public function buildDisplay($options) {
    $options += ["tab" => ""];
    // layout
    $this->layout->add(["top", "left" => "div.col-md-9", "right" => "div.col-md-3"]);
    $this->layout->add(["bottom", "tabs" => "div.col-sm-12"]);
    $this->layout->put("tabs", 'div[data-dojo-type="dijit/layout/TabContainer"][data-dojo-props="doLayout:false, tabPosition:\'left-h\'"][style="width:100%;height:100%"]', '', 'tc');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="URL path"]'.((empty($options['tab'])) ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'path');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Parent"]'.(($options['tab'] === "breadcrumbs") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'breadcrumbs');
    // left
    $this->add(["name", "pane" => "left"]);
    $this->add(["description", "pane" => "left", "class" => "rich-text"]);
    // right
    $this->add(["path", "label" => "URL path", "info" => "Leave empty to generate automatically", "input_type" => "text", "pane" => "path"]);
    $this->add(["parent", "info" => "Start typing the title of the page and autocomplete results will display", "data-dojo-type" => "sb/form/Autocomplete", "data-dojo-props" => "model: 'categories'", "pane" => "breadcrumbs"]);
  }
}
