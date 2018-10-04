<?php
namespace Starbug\Core;
class Menus extends MenusModel {

	public $label_select = "CONCAT(menus.menu, ': ', menus.content)";

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
}
