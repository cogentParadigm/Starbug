<?php
class AdminMenusController {
	public $routes = array(
		'menu' => '{menu}'
	);
	function init() {
		$this->assign("model", "menus");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		$this->assign("menu", $this->request->parameters['menu']);
		if (success("menus", "create")) {
			$menu = get("menus", sb("menus")->insert_id);
			redirect(uri("admin/menus/menu/".$menu['menu']));
		} else $this->render("admin/create");
	}
	function update($id = null) {
		$this->assign("id", $id);
		if (success("menus", "create")) {
			$menu = get("menus", $id);
			redirect(uri("admin/menus/menu/".$menu['menu']));
		} else $this->render("admin/update");
	}
	function menu($menu) {
		$this->assign("menu", $menu);
		$this->render("admin/menus/menu");
	}
}
?>
