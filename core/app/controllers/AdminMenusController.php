<?php
namespace Starbug\Core;

class AdminMenusController extends Controller {
  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models) {
    $this->db = $db;
    $this->menus = $models->get("menus");
  }
  public function init() {
    $this->assign("model", "menus");
  }
  public function defaultAction() {
    $this->render("admin/list.html");
  }
  public function create() {
    $this->assign("menu", $this->request->getQueryParams()['menu']);
    if ($this->db->success("menus", "create")) {
      $menu = $this->db->get("menus", $this->menus->insert_id);
      $this->response->redirect("admin/menus/menu/".$menu['menu']);
    } else $this->render("admin/create.html");
  }
  public function update($id) {
    $this->assign("id", $id);
    if ($this->db->success("menus", "create")) {
      $menu = $this->db->get("menus", $id);
      $this->response->redirect("admin/menus/menu/".$menu['menu']);
    } else $this->render("admin/update.html");
  }
  public function menu($menu) {
    $this->assign("menu", $menu);
    $this->render("admin/menus/menu.html");
  }
  public function import() {
    $this->render("admin/import.html");
  }
}
