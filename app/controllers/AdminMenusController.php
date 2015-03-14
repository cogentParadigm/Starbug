<?php
class AdminMenusController {
	function init() {
		$this->assign("model", "menus");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		$this->assign("menu", $_GET['menu']);
		if (success("menus", "create")) {
			$menu = get("menus", sb("menus")->insert_id);
			redirect(uri("admin/menus/menu/".$menu['menu']));
		} else $this->render("admin/menus/create");
	}
	function update($id=null) {
		$this->assign("id", $id);
		if (success("menus", "create")) {
			$menu = get("menus", $id);
			redirect(uri("admin/menus/menu/".$menu['menu']));
		} else $this->render("admin/menus/update");
	}
	function menu($menu=null) {
		$this->assign("menu", $menu);
		$this->render("admin/menus/menu");
	}
}
?>
