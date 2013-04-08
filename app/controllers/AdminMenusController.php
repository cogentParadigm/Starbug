<?php
class AdminMenusController {
	function init() {
		assign("model", "menus");
	}
	function default_action() {
		$this->render("admin/menus/list");
	}
	function create() {
		assign("menu", $_GET['menu']);
		if (success("menus", "create")) {
			$menu = get("menus", sb("menus")->insert_id);
			redirect(uri("admin/menus/menu/".$menu['menu']));
		} else $this->render("admin/menus/create");
	}
	function update($id=null) {
		assign("id", $id);
		if (success("menus", "create")) {
			$menu = get("menus", $id);
			redirect(uri("admin/menus/menu/".$menu['menu']));
		} else $this->render("admin/menus/update");
	}
	function menu($menu=null) {
		assign("menu", $menu);
		$this->render("admin/menus/menu");
	}
}
?>
