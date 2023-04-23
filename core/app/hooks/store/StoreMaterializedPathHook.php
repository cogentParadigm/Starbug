<?php
namespace Starbug\Core;

use Starbug\Db\DatabaseInterface;
use Starbug\Db\Query\ExecutorHook;

class StoreMaterializedPathHook extends ExecutorHook {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function validate($query, $key, $value, $column, $argument) {
    if (empty($value) || $value == "NULL") {
      $query->set($argument, "");
    } else {
      $parent = $this->db->get($query->model, $value);
      $query->set($argument, (empty($parent[$argument]) ? '-' : $parent[$argument]).$parent['id']."-");
    }
    return $value;
  }
}
