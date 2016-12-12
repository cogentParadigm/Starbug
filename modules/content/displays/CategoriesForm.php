<?php
namespace Starbug\Content;
use Starbug\Core\FormDisplay;
class CategoriesForm extends FormDisplay {
	public $model = "categories";
	public $cancel_url = "admin/categories";
	function build_display($options) {
		//layout
		$this->layout->add(["top", "left" => "div.col-md-9", "right" => "div.col-md-3"]);
		$this->layout->add(["bottom", "tabs" => "div.col-sm-12"]);
		$this->layout->put("tabs", 'div[data-dojo-type="dijit/layout/TabContainer"][data-dojo-props="doLayout:false, tabPosition:\'left-h\'"][style="width:100%;height:100%"]', '', 'tc');
		$this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Machine Name"]'.((empty($ops['tab'])) ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'path');
		$this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Parent"]'.(($ops['tab'] === "breadcrumbs") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'breadcrumbs');
		//left
		$this->add(["name", "pane" => "left"]);
		$this->add(["description", "pane" => "left", "class" => "rich-text"]);
		$this->add(["images", "pane" => "left", "input_type" => "file_select", "size" => 0]);
		//right
		$this->add(["groups", "taxonomy" => "groups", "input_type" => "multiple_category_select", "pane" => "right"]);
		$this->add(["slug", "label" => "Machine Name", "info" => "Leave empty to generate automatically", "pane" => "path"]);
		//$display->add(["breadcrumb", "label" => "Breadcrumbs Title", "style" => "width:100%", "pane" => "breadcrumbs"]);
		$this->add(["parent", "info" => "Start typing the title of the page and autocomplete results will display", "input_type" => "autocomplete", "from" => "categories", "pane" => "breadcrumbs"]);
	}
}
?>
