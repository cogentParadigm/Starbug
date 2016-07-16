<?php
namespace Starbug\App;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\ModelFactoryInterface;
class AdminMenusController extends Controller {
	public $routes = array(
		'menu' => '{menu}',
		'update' => '{id}'
	);
	function __construct(DatabaseInterface $db, ModelFactoryInterface $models) {
		$this->db = $db;
		$this->menus = $models->get("menus");
	}
	function init() {
		$this->assign("model", "menus");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		$this->assign("menu", $this->request->getParameter('menu'));
		if ($this->db->success("menus", "create")) {
			$menu = $this->db->get("menus", $this->menus->insert_id);
			$this->redirect("admin/menus/menu/".$menu['menu']);
		} else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		if ($this->db->success("menus", "create")) {
			$menu = $this->db->get("menus", $id);
			$this->redirect("admin/menus/menu/".$menu['menu']);
		} else $this->render("admin/update");
	}
	function menu($menu) {
		$this->assign("menu", $menu);
		$this->render("admin/menus/menu");
	}
	function import() {
		$this->render("admin/import");
	}
}
?>
