<?php
class Uris {

	function create($uris) {
		/*
		if ($uris['type'] != "View" && $uris['type'] != $_POST['type']) {
			$uris['layout'] = $uris['type'];
			$uris['type'] = $_POST['type'];
		}
		if ($_POST['type'] == "Post") $uris['path'] = "blog/".$uris['path'];
		*/
		queue("blocks", array("type" => "text",  "region" => "content",  "position" => 1, "uris_id" => "", "content" => $_POST['block-content-1']['content']));
		$type = query("content_types")->condition("type", $uris['type'])->one();
		if (!empty($type['table'])) {
			$data = $_POST[$type['table']];
			$data['uris_id'] = "";
			sb($type['table'])->create($data);
		}
		$this->store($uris);
		if (!errors()) {
			$uid = $this->insert_id;
		} else {
			global $sb;
			if (errors("uris[title]") && empty($uris['path'])) unset($sb->errors['uris']['path']);
		}
	}
	
	function update($uris) {
		/*
		if ($uris['type'] != "View" && $uris['type'] != $_POST['type']) {
			$uris['layout'] = $uris['type'];
			$uris['type'] = $_POST['type'];
		}
		if ($_POST['type'] == "Post") $uris['path'] = "blog/".$uris['path'];
		*/
		$row = $this->get($uris['id']);
		$type = query("content_types")->condition("type", $uris['type'])->one();
		if (!empty($type['table'])) {
			$data = $_POST[$type['table']];
			$data['uris_id'] = $uris['id'];
			sb($type['table'])->update($data);
		}
		$this->store($uris);
		if (!errors()) {
			$uid = $uris['id'];
			$blocks = get("blocks", array("uris_id" => $uris['id']));
			foreach ($blocks as $block) {
				$key = 'block-'.$block['region'].'-'.$block['position'];
				if (!empty($_POST[$key])) store("blocks", array("id" => $block['id'], "content" => $_POST[$key]["content"]));
			}
		}
	}

	function delete($uris) {
		$id = intval($uris['id']);
		$uris = query("uris")->condition("id", $uris['id'])->one();
		$type = query("content_types")->condition("type", $uris['type'])->one();
		$cond = array("uris_id" => $uris['id']);
		if (!empty($type['table'])) remove($type['table'], $cond);
		remove("blocks", $cond);
		remove("uris", "id:".$id);
	}
	
	function query_admin($query, &$ops) {
		$query->select("uris.*,uris.statuses.term as statuses");
		if (!logged_in("admin")) $query->action("read");
		$query->condition("uris.prefix", "app/views/");
		$query->condition("uris.statuses", "deleted", "!=");
		if (!empty($ops['type'])) {
			$query->condition("uris.type", $ops['type']);
		}
		if (!empty($ops['status'])) $query->condition("uris.statuses.id", $ops['status']);
		efault($ops['orderby'], "uris.modified DESC, uris.created DESC, uris.title DESC");
		$query->sort($ops['orderby']);
		return $query;
	}
	
	function display_admin($display, $options) {
		$display->add("title", "type", "statuses");
	}
	
	function display_form($display, &$ops) {
		if (empty($_POST['uris']['type'])) $_POST['uris']['type'] = $ops['type'];
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
		$display->add("type  pane:right  input_type:hidden  default:".$ops['type']);
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
		//content type
		$type = query("content_types")->condition("type", $display->get("type"))->one();
		if (!empty($type['table'])) {
			$items = $display->items;
			$display->model = $type['table'];
			$display->query();
			sb($type['table'])->display_form($display, $ops);
			$display->model = "uris";
			$display->items = $items;
		}
	}


}
?>
