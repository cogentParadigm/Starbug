<?php
namespace Starbug\Content;

use Starbug\Core\Collection;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\DatabaseInterface;

class AdminCategoriesCollection extends Collection {
  public function __construct(ModelFactoryInterface $models, DatabaseInterface $db) {
    $this->models = $models;
    $this->db = $db;
  }
  public function build($query, $ops) {
    $query->select("categories.*,(SELECT COUNT(*) FROM ".$this->db->prefix("categories")." as c WHERE c.parent=categories.id) as children");
    if (!empty($ops['parent'])) $query->condition("parent", $ops['parent']);
    else $query->condition("categories.parent", 0);
    $query->sort("categories.position");
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as &$row) {
      $depth = 0;
      if (!empty($row['tree_path'])) {
        $tree = $row['tree_path'];
        $depth = substr_count($tree, "-")-1;
      }
      if ($depth > 0) $row['name'] = str_pad(" ".$row['name'], strlen(" ".$row['name'])+$depth, "-", STR_PAD_LEFT);
    }
    return $rows;
  }
}
