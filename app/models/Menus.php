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

	function query_admin($query, &$ops) {
		$query = parent::query_admin($query, $ops);
		$query->select("DISTINCT menu");
		return $query;
	}
	
	function query_tree($query, &$ops) {
		$query->select("menus.*,menus.uris_id.title,(SELECT COUNT(*) FROM ".P("menus")." as t WHERE t.parent=menus.id) as children");
		$query->condition("menus.menu", $ops['menu']);
		if (!empty($ops['parent'])) $query->condition("menus.parent", $ops['parent']);
		else $query->condition("menus.parent", 0);
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
