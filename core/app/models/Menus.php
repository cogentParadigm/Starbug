<?php
namespace Starbug\Core;

class Menus extends MenusModel {

  public function create($menu) {
    if (!isset($menu['position'])) {
      $menu['position'] = "";
    }
    if (!isset($menu['template'])) {
      $menu['template'] = "";
    }
    if (!isset($menu['target'])) {
      $menu['target'] = "";
    }
    $this->store($menu);
  }

  public function deleteMenu($menu) {
    $this->db->query("menus")->condition("menu", $menu['menu'])->delete();
  }
}
