<?php
namespace Starbug\Content;

use Starbug\Core\FormDisplay;

class PagesForm extends FormDisplay {
  public $model = "pages";
  public $cancel_url = "admin/pages";
  public $collection = "PagesForm";
  public function buildDisplay($options) {
    $options += ["tab" => ""];
    // layout
    $this->layout->add(["top", "left" => "div.col-md-9", "right" => "div.col-md-3"]);
    $this->layout->add(["bottom", "tabs" => "div.col-sm-12"]);
    $this->layout->put("tabs", 'div[data-dojo-type="dijit/layout/TabContainer"][data-dojo-props="doLayout:false, tabPosition:\'left-h\'"][style="width:100%;height:100%"]', '', 'tc');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="URL path"]'.((empty($options['tab'])) ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'path');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Meta tags"]'.(($options['tab'] === "meta") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'meta');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Breadcrumbs"]'.(($options['tab'] === "breadcrumbs") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'breadcrumbs');
    // left
    $this->add(["title", "input_type" => "text", "pane" => "left"]);
    $this->add(["blocks", "input_type" => "blocks", "pane" => "left"]);
    $this->add(["images", "input_type" => "text", "data-dojo-type" => "sb/form/FileList", "data-dojo-props" => "selectionParams: {size: 0}, browseEnabled: true"]);
    // right
    $this->add(["published", "input_type" => "checkbox", "value" => 1, "pane" => "right"]);
    $this->add(["categories", "input_type" => "multiple_select", "from" => "categories", "query" => "Select", "pane" => "right"]);
    // tabs
    $this->add(["path", "label" => "URL path", "info" => "Leave empty to generate automatically", "input_type" => "text", "pane" => "path"]);
    $this->add(["description", "label" => "Meta Description", "input_type" => "textarea", "class" => "plain", "style" => "width:100%", "data-dojo-type" => "dijit/form/Textarea", "pane" => "meta"]);
    $this->add(["meta_keywords", "label" => "Meta Keywords", "input_type" => "textarea", "class" => "plain", "style" => "width:100%", "data-dojo-type" => "dijit/form/Textarea", "pane" => "meta"]);
    $this->add(["canonical", "input_type" => "text", "label" => "Canonical URL", "style" => "width:100%", "pane" => "meta"]);
    $this->add(["meta", "label" => "Custom Meta Tags", "input_type" => "textarea", "class" => "plain", "style" => "width:100%", "data-dojo-type" => "dijit/form/Textarea", "pane" => "meta"]);
    $this->add(["breadcrumb", "input_type" => "text", "label" => "Breadcrumbs Title", "style" => "width:100%", "pane" => "breadcrumbs"]);
    $this->add(["parent", "info" => "Start typing the title of the page and autocomplete results will display", "data-dojo-type" => "sb/form/Autocomplete", "data-dojo-props" => "model: 'pages'", "pane" => "breadcrumbs"]);
  }
}
