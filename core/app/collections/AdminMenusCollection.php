<?php
namespace Starbug\Core;

class AdminMenusCollection extends Collection {
  public function build($query, $ops) {
    $query->removeSelection();
    $query->select("DISTINCT menu");
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as &$row) {
      $row['id'] = $row['menu'];
    }
    return $rows;
  }
}
