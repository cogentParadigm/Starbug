<?php
namespace Starbug\Core;
class UrisForm extends FormDisplay {
	public $model = "uris";
	public $cancel_url = "admin/uris";
	function build_display($options) {
		//layout
		$this->layout->add("top  left:div.col-md-9  right:div.col-md-3", "bottom  tabs:div.col-sm-12");
		$this->layout->put("tabs", 'div[data-dojo-type="dijit/layout/TabContainer"][data-dojo-props="doLayout:false, tabPosition:\'left-h\'"][style="width:100%;height:100%"]', '', 'tc');
		$this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="URL path"]'.((empty($ops['tab'])) ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'path');
		$this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Meta tags"]'.(($ops['tab'] === "meta") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'meta');
		$this->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Breadcrumbs"]'.(($ops['tab'] === "breadcrumbs") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'breadcrumbs');
		//left
		$this->add("title  pane:left");
		$this->add("blocks  input_type:blocks  pane:left");
		$this->add("images  pane:left  input_type:file_select  size:0");
		//right
		$this->add("statuses  label:Status  taxonomy:statuses  default:pending  input_type:category_select  pane:right");
		$this->add("groups  taxonomy:groups  input_type:multiple_category_select  pane:right");
		$this->add("categories  input_type:multiple_category_select  taxonomy:uris_categories  pane:right");
		$this->add("tags  input_type:tag_select  pane:right");
		//tabs
		$this->add("path  label:URL path  info:Leave empty to generate automatically  pane:path");
		$this->add("description  label:Meta Description  input_type:textarea  class:plain  style:width:100%  data-dojo-type:dijit/form/Textarea  pane:meta");
		$this->add("meta_keywords  label:Meta Keywords  input_type:textarea  class:plain  style:width:100%  data-dojo-type:dijit/form/Textarea  pane:meta");
		$this->add("canonical  label:Canonical URL  style:width:100%  pane:meta");
		$this->add("meta  label:Custom Meta Tags  input_type:textarea  class:plain  style:width:100%  data-dojo-type:dijit/form/Textarea  pane:meta");
		$this->add("breadcrumb  label:Breadcrumbs Title  style:width:100%  pane:breadcrumbs");
		$this->add("parent  info:Start typing the title of the page and autocomplete results will display  input_type:autocomplete  pane:breadcrumbs");

	}
}
?>
