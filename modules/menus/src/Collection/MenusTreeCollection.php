<?php
namespace Starbug\Menus\Collection;

use Starbug\Db\Collection;

class MenusTreeCollection extends Collection {
  public function build($query, $ops) {
    $query->select("menus.*");
    $query->select("(SELECT COUNT(*) FROM ".$this->db->prefix("menus")." as t WHERE t.parent=menus.id) as children");
    if (!empty($ops['parent'])) {
      $query->condition("menus.parent", $ops['parent']);
    } else {
      $query->condition("menus.parent", 0);
      $query->condition("menus.menu", $ops['menu']);
    }
    $query->sort("menus.menu_path ASC, menus.position ASC");
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as $idx => $item) {
      if ($item["template"] === "divider") {
        $item['content'] = "(divider)";
      }
      $depth = 0;
      if (!empty($item['menu_path'])) {
        $tree = $item['menu_path'];
        $depth = substr_count($tree, "-")-1;
      }
      if ($depth > 0) {
        $item['content'] = str_pad(" ".$item['content'], strlen(" ".$item['content'])+$depth, "-", STR_PAD_LEFT);
      }
      $rows[$idx] = $item;
    }
    return $rows;
  }
}
