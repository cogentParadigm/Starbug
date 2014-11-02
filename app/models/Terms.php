<?php
/**
 * terms model
 * @ingroup models
 */
class Terms {

	function create($term) {
		if (!empty($term['term'])) {
			$term['term'] = normalize($term['term']);
			$term['slug'] = strtolower(str_replace(" ", "-", $term['term']));
		}
		if(empty($term['id'])) efault($term['position'], '');
		$this->store($term);
		if (errors('terms[slug]') && !empty($term['term'])) foreach (errors("terms[slug]", true) as $e) error(str_replace("slug", "term", $e), "term");
	}

	function delete($term) {
		try {
			query("terms")->condition("id", $term['id'])->delete();
		} catch (Exception $e) {

		}
		$term = query("terms")->condition("id", $term['id'])->one();
		if ($term) error("This term must be detached from all entities before it can be deleted.", "global");
	}

	function delete_taxonomy($term) {
		$tax = $term['taxonomy'];
		$this->remove("taxonomy:$tax");
	}

	function query_admin($query, &$ops) {
		$query = parent::query_admin($query, $ops);
		$query->undo("select");
		$query->select("DISTINCT terms.taxonomy");
		return $query;
	}

	function query_list($query, &$ops) {
		$query->sort("terms.term_path ASC, terms.position ASC");
		return $query;
	}

	function query_filters($action, $query, &$ops) {
		$query = parent::query_filters($action, $query, $ops);
		if (!empty($ops['taxonomy'])) {
			$query->condition("terms.taxonomy", $ops['taxonomy']);
		}
		return $query;
	}

	function query_tree($query, &$ops) {
		$query->select("terms.*,(SELECT COUNT(*) FROM ".P("terms")." as t WHERE t.parent=terms.id) as children");
		if (!empty($ops['parent'])) $query->condition("parent", $ops['parent']);
		else $query->condition("terms.parent", 0);
		$query->sort("terms.position");
		return $query;
	}

	function display_admin($display, $ops) {
		$display->add("taxonomy", "row_options  plugin:starbug.grid.columns.taxonomy_options");
	}

	function display_tree($display, $ops) {
		$display->insert(0, "id  plugin:starbug.grid.columns.tree  sortable:false");
		$display->add("term  sortable:false", "position  sortable:false");
	}

	function display_form($display, &$ops) {
		//layout
		$display->layout->add("top  left:div.col-md-9  right:div.col-md-3", "bottom  tabs:div.col-sm-12");
		$display->layout->put("tabs", 'div[data-dojo-type="dijit/layout/TabContainer"][data-dojo-props="doLayout:false, tabPosition:\'left-h\'"][style="width:100%;height:100%"]', '', 'tc');
		$display->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Machine Name"]'.((empty($_GET['tab'])) ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'path');
		$display->layout->put("tc", 'div[data-dojo-type="dijit/layout/ContentPane"][title="Parent"]'.(($_GET['tab'] === "breadcrumbs") ? '[data-dojo-props="selected:true"]' : '').'[style="min-height:200px"]', '', 'breadcrumbs');
		//left
		$display->add("term  pane:left");
		if (!empty($_GET['new'])) $display->add("taxonomy  pane:left  input_type:text");
		else $display->add("taxonomy  pane:left  default:".filter_string($_GET['taxonomy']));
		$display->add("description  pane:left  class:rich-text");
		//$display->add("blocks  input_type:blocks  pane:left");
		$display->add("images  pane:left  input_type:file_select  size:0");
		//right
		$display->add("groups  taxonomy:groups  input_type:multiple_category_select  pane:right");
		$display->add("slug  label:Machine Name  info:Leave empty to generate automatically  pane:path");
		//$display->add("breadcrumb  label:Breadcrumbs Title  style:width:100%  pane:breadcrumbs");
		$display->add("parent  info:Start typing the title of the page and autocomplete results will display  input_type:autocomplete  pane:breadcrumbs");
	}

	function filter($item, $action) {
		if ($action === "tree") {
			$depth = 0;
			if (!empty($item['term_path'])) {
				$tree = $item['term_path'];
				$depth = substr_count($tree, "-")-1;
			}
			if ($depth > 0) $item['term'] = str_pad(" ".$item['term'], strlen(" ".$item['term'])+$depth, "-", STR_PAD_LEFT);
		} else if ($action === "admin") {
			$item['id'] = $item['taxonomy'];
		}
		return $item;
	}

}
?>
