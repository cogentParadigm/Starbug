<?php
/**
 * menus model
 * @ingroup models
 */
namespace Starbug\Core;
class Menus extends MenusModel {

	function create($menu) {
		if (!isset($menu['position'])) $menu['position'] = "";
		if (!isset($menu['template'])) $menu['template'] = "";
		if (!isset($menu['target'])) $menu['target'] = "";
		$this->store($menu);
	}

	function delete($menu) {
		$this->remove($menu['id']);
	}

	function delete_menu($menu) {
		$this->db->query("menus")->condition("menu", $menu['menu'])->delete();
	}

	function query_admin($query, &$ops) {
		$query = parent::query_admin($query, $ops);
		$query->undo("select");
		$query->select("DISTINCT menu");
		return $query;
	}

	function query_tree($query, &$ops) {
		$query->select("menus.uris_id.title,(SELECT COUNT(*) FROM ".$this->db->prefix("menus")." as t WHERE t.parent=menus.id) as children");
		if (!empty($ops['parent'])) $query->condition("menus.parent", $ops['parent']);
		else {
			$query->condition("menus.parent", 0);
			$query->condition("menus.menu", $ops['menu']);
		}
		$query->sort("menus.menu_path ASC, menus.position ASC");
		return $query;
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
