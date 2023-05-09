<?php
namespace Starbug\Core;

use Starbug\Db\DatabaseInterface;
use Starbug\Db\Query\ExecutorHook;

class StoreGroupsHook extends ExecutorHook {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function emptyBeforeInsert($query, $column, $argument) {
    $query->set($column, $this->validate($query, $column, "", $column, $argument));
  }
  public function validate($query, $key, $value, $column, $argument) {
    $user = $this->db->query("groups")->condition("slug", "user")->one();
    if (empty($value)) {
      $value = [];
    } elseif (!is_array($value)) {
      $value = explode(",", preg_replace("/[,\s]*,[,\s]*/", ",", $value));
    }
    if (!empty($user) && !in_array($user["id"], $value)) {
      $value[] = $user["id"];
    }
    return $value;
  }
}
