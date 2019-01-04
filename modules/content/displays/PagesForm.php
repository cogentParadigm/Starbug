<?php
namespace Starbug\Content;

use Starbug\Core\FormDisplay;

class PagesForm extends FormDisplay {
  public $model = "pages";
  public $cancel_url = "admin/pages";
  public $collection = "PagesForm";
  public function buildDisplay($options) {
    // layout
    $this->layout->add(["top", "left" => "div.col-md-9", "right" => "div.col-md-3"]);
    $this->layout->add(["bottom", "tabs" => "div.col-sm-12"]);
    $this->layout->put("tabs", 'div[data-dojo-type="dijit/layout/TabContainer"][data-dojo-props="doLayout:false, tabPosition:\'left-h\'"][style="width:100%;height:100%"]', '', 'tc');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="URL path"]'.((empty($ops['tab'])) ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'path');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Meta tags"]'.(($ops['tab'] === "meta") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'meta');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Breadcrumbs"]'.(($ops['tab'] === "breadcrumbs") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'breadcrumbs');
    // left
    $this->add(["title", "pane" => "left"]);
    $this->add(["blocks", "input_type" => "blocks", "pane" => "left"]);
    $this->add(["images", "pane" => "left", "input_type" => "file_select", "size" => "0"]);
    // right
    $this->add(["published", "pane" => "right"]);
    $this->add(["categories", "pane" => "right"]);
    $this->add(["tags", "input_type" => "tag_select", "pane" => "right"]);
    // tabs
    $this->add(["path", "label" => "URL path", "info" => "Leave empty to generate automatically", "input_type" => "text", "pane" => "path"]);
    $this->add(["description", "label" => "Meta Description", "input_type" => "textarea", "class" => "plain", "style" => "width:100%", "data-dojo-type" => "dijit/form/Textarea", "pane" => "meta"]);
    $this->add(["meta_keywords", "label" => "Meta Keywords", "input_type" => "textarea", "class" => "plain", "style" => "width:100%", "data-dojo-type" => "dijit/form/Textarea", "pane" => "meta"]);
    $this->add(["canonical", "label" => "Canonical URL", "style" => "width:100%", "pane" => "meta"]);
    $this->add(["meta", "label" => "Custom Meta Tags", "input_type" => "textarea", "class" => "plain", "style" => "width:100%", "data-dojo-type" => "dijit/form/Textarea", "pane" => "meta"]);
    $this->add(["breadcrumb", "label" => "Breadcrumbs Title", "style" => "width:100%", "pane" => "breadcrumbs"]);
    $this->add(["parent", "info" => "Start typing the title of the page and autocomplete results will display", "input_type" => "autocomplete", "from" => "pages", "pane" => "breadcrumbs"]);
  }
}
