<?php
namespace Starbug\Content;

use Starbug\Core\Collection;

class AdminTagsCollection extends Collection {
  public function build($query, $ops) {
    $query->select("tags.*,(SELECT COUNT(*) FROM ".$this->db->prefix("tags")." as c WHERE c.parent=tags.id) as children");
    if (!empty($ops['parent'])) {
      $query->condition("parent", $ops['parent']);
    } else {
      $query->condition("tags.parent", 0);
    }
    $query->sort("tags.position");
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as &$row) {
      $depth = 0;
      if (!empty($row['tree_path'])) {
        $tree = $row['tree_path'];
        $depth = substr_count($tree, "-")-1;
      }
      if ($depth > 0) {
        $row['name'] = str_pad(" ".$row['name'], strlen(" ".$row['name'])+$depth, "-", STR_PAD_LEFT);
      }
    }
    return $rows;
  }
}
