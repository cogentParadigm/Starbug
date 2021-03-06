<?php
namespace Starbug\Core;

class TermsForm extends FormDisplay {
  public $model = "terms";
  public $cancel_url = "admin/taxonomies";
  public function buildDisplay($options) {
    $options += ["tab" => ""];
    $taxonomy = $options["taxonomy"] ?? $this->get("taxonomy") ?? "";
    // layout
    $this->layout->add(["top", "left" => "div.col-9-ns.col-6", "right" => "div.col-3-ns.col-6"]);
    $this->layout->add(["bottom", "tabs" => "div.col-sm-12"]);
    $this->layout->put("tabs", 'div[data-dojo-type="dijit/layout/TabContainer"][data-dojo-props="doLayout:false, tabPosition:\'left-h\'"][style="width:100%;height:100%"]', '', 'tc');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Machine Name"]'.((empty($options['tab'])) ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'path');
    $this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Parent"]'.(($options['tab'] === "breadcrumbs") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'breadcrumbs');
    // left
    $this->add(["term", "pane" => "left"]);
    if (!empty($options['new'])) $this->add(["taxonomy", "pane" => "left", "input_type" => "text"]);
    else $this->add(["taxonomy", "pane" => "left", "default" => $options['taxonomy'] ?? ""]);
    $this->add(["description", "pane" => "left", "class" => "rich-text"]);
    // $display->add(["blocks", "input_type" => "blocks", "pane" => "left"]);
    $this->add(["images", "pane" => "left", "input_type" => "text", "data-dojo-type" => "sb/form/FileList", "data-dojo-props" => "selectionParams: {size: 0}, browseEnabled: true"]);
    // right
    $this->add(["groups", "taxonomy" => "groups", "input_type" => "multiple_category_select", "pane" => "right"]);
    $this->add(["slug", "label" => "Machine Name", "info" => "Leave empty to generate automatically", "pane" => "path"]);
    // $display->add(["breadcrumb", "label" => "Breadcrumbs Title", "style" => "width:100%", "pane" => "breadcrumbs"]);
    $this->add(["parent", "info" => "Start typing the title of the page and autocomplete results will display", "data-dojo-type" => "sb/form/Autocomplete", "data-dojo-props" => "model:'terms', query:{taxonomy:'".$taxonomy."'}", "pane" => "breadcrumbs"]);
  }
}
