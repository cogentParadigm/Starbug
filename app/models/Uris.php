<?php
class Uris {

	function query_admin($query, &$ops) {
		$query->select($query->model.".statuses.term as statuses");
		if (!logged_in("admin")) $query->action("read");
		$query->condition($query->model.".prefix", "app/views/");
		if (!empty($ops['type'])) {
			$query->condition($query->model.".type", $ops['type']);
		}
		if (!empty($ops['status'])) $query->condition($query->model.".statuses.id", $ops['status']);
		else $query->condition($query->model.".statuses.slug", "deleted", "!=");
		efault($ops['orderby'], "modified DESC, created DESC, title DESC");
		$query->sort($ops['orderby']);
		return $query;
	}

	function display_admin($display, $options) {
		$display->add("title", "statuses", "modified  label:Last Modified");
	}

	function display_form($display, &$ops) {
		//layout
		$display->layout->add("top  left:div.col-md-9  right:div.col-md-3", "bottom  tabs:div.col-sm-12");
		$display->layout->put("tabs", 'div[data-dojo-type="dijit/layout/TabContainer"][data-dojo-props="doLayout:false, tabPosition:\'left-h\'"][style="width:100%;height:100%"]', '', 'tc');
		$display->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="URL path"]'.((empty($_GET['tab'])) ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'path');
		$display->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Meta tags"]'.(($_GET['tab'] === "meta") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'meta');
		$display->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Breadcrumbs"]'.(($_GET['tab'] === "breadcrumbs") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'breadcrumbs');
		//left
		$display->add("title  pane:left");
		$display->add("blocks  input_type:blocks  pane:left");
		$display->add("images  pane:left  input_type:file_select  size:0");
		//right
		$display->add("statuses  label:Status  taxonomy:statuses  default:pending  input_type:category_select  pane:right");
		$display->add("groups  taxonomy:groups  input_type:multiple_category_select  pane:right");
		$display->add("categories  input_type:multiple_category_select  pane:right");
		$display->add("tags  input_type:tag_select  pane:right");
		//tabs
		$display->add("path  label:URL path  info:Leave empty to generate automatically  pane:path");
		$display->add("description  label:Meta Description  input_type:textarea  class:plain  style:width:100%  data-dojo-type:dijit/form/Textarea  pane:meta");
		$display->add("meta_keywords  label:Meta Keywords  input_type:textarea  class:plain  style:width:100%  data-dojo-type:dijit/form/Textarea  pane:meta");
		$display->add("canonical  label:Canonical URL  style:width:100%  pane:meta");
		$display->add("meta  label:Custom Meta Tags  input_type:textarea  class:plain  style:width:100%  data-dojo-type:dijit/form/Textarea  pane:meta");
		$display->add("breadcrumb  label:Breadcrumbs Title  style:width:100%  pane:breadcrumbs");
		$display->add("parent  info:Start typing the title of the page and autocomplete results will display  input_type:autocomplete  pane:breadcrumbs");
	}


}
?>
