<?php
/**
 * menus model
 * @ingroup models
 */
class Menus {

	function create($menu) {
		if (!isset($menu['position'])) $menu['position'] = "";
		if (!isset($menu['template'])) $menu['template'] = "";
		if (!isset($menu['target'])) $menu['target'] = "";
		$this->store($menu);
	}

	function delete($menu) {
		return $this->remove('id:'.$menu['id']);
	}

	function delete_menu($menu) {
		query("menus")->condition("menu", $menu['menu'])->delete();
	}

	function query_admin($query, &$ops) {
		$query = parent::query_admin($query, $ops);
		$query->undo("select");
		$query->select("DISTINCT menu");
		return $query;
	}

	function query_tree($query, &$ops) {
		$query->select("menus.uris_id.title,(SELECT COUNT(*) FROM ".P("menus")." as t WHERE t.parent=menus.id) as children");
		if (!empty($ops['parent'])) $query->condition("menus.parent", $ops['parent']);
		else {
			$query->condition("menus.parent", 0);
			$query->condition("menus.menu", $ops['menu']);
		}
		$query->sort("menus.menu_path ASC, menus.position ASC");
		return $query;
	}

	function display_admin($display, $ops) {
		$display->add("menu", "row_options  plugin:starbug.grid.columns.menu_options");
	}

	function display_tree($display, $ops) {
		$display->insert(0, "id  plugin:starbug.grid.columns.tree  sortable:false");
		$display->add("content  label:Title  plugin:starbug.grid.columns.html  sortable:false", "position  sortable:false");
	}

	function display_form($display, $ops) {
		$display->layout->add("top  tl:div.col-md-6  tr:div.col-md-6");
		$display->layout->add("middle  ml:div.col-md-6  mr:div.col-md-6");
		$display->layout->add("bottom  bl:div.col-md-6  br:div.col-md-6");
		$display->add("menu  input_type:hidden  pane:tl  default:".$ops['menu']);
		$display->add("parent  input_type:autocomplete  pane:tl  info:Leave empty to place the item at the top level.");
		$display->add("position  pane:tr  info:Enter 1 for the first position, leave empty for the last.");
		$display->add("uris_id  pane:ml  input_type:autocomplete  from:uris  label:Page  info:Select a page.");
		$display->add("href  pane:mr  label:URL  info:Enter a URL manually.");
		$display->add("content  pane:bl  info:Override the link text.");
		$display->add("target  pane:br  input_type:checkbox  label:Open in new tab/window  value:_blank");
		$display->add("template  pane:br  input_type:checkbox  label:Divider  value:divider");
	}

	function filter($item, $action) {
		if ($action === "tree") {
			if (empty($item['content']) && !empty($item['title'])) $item['content'] = $item['title'];
			if ($item['template'] === "divider") $item['content'] = "(divider)";
			$depth = 0;
			if (!empty($item['menu_path'])) {
				$tree = $item['menu_path'];
				$depth = substr_count($tree, "-")-1;
			}
			if ($depth > 0) $item['content'] = str_pad(" ".$item['content'], strlen(" ".$item['content'])+$depth, "-", STR_PAD_LEFT);
		} else if ($action === "admin") {
			$item['id'] = $item['menu'];
		}
		return $item;
	}


}
?>
