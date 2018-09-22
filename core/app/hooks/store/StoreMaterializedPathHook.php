<?php
namespace Starbug\Core;

class StoreMaterializedPathHook extends QueryHook {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function validate($query, $key, $value, $column, $argument) {
    if (empty($value)) $query->set($argument, "");
    else {
      $parent = $this->db->get($query->model, $value);
      $query->set($argument, (empty($parent[$argument]) ? '-' : $parent[$argument]).$parent['id']."-");
    }
    return $value;
  }
}
