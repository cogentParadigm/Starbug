<?php
namespace Starbug\Core;

class MenuCollection extends Collection {
  public $model = "menus";
  public function build($query, $ops) {
    $query->condition("menus.menu", $ops['menu']);
    $query->sort("menus.menu_path ASC, menus.position ASC");
    $query->select([
      "id",
      "menu",
      "parent",
      "href",
      "content",
      "icon",
      "target",
      "template",
      "menu_path"
    ], "menus");
    return $query;
  }
  public function filterRows($rows) {
    $links = [];
    foreach ($rows as $idx => $link) {
      $link["children"] = [];
      if ($link["parent"] == 0) {
        $links[$link["id"]] = $link;
      } else {
        $chain = explode("-", trim($link["menu_path"], "-"));
        $parent = &$links[array_shift($chain)];
        foreach ($chain as $c) {
          $parent = &$parent['children'][$c];
        }
        $parent['children'][$link['id']] = $link;
      }
    }
    return $links;
  }
}
