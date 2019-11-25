<?php
namespace Starbug\Core;

class StoreGroupsHook extends QueryHook {
  public function emptyBeforeInsert($query, $column, $argument) {
    $query->set($column, $this->validate($query, $column, "", $column, $argument));
  }
  public function validate($query, $key, $value, $column, $argument) {
    if (empty($value)) $value = [];
    elseif (!is_array($value)) $value = explode(",", preg_replace("/[,\s]*,[,\s]*/", ",", $value));
    if (!in_array("user", $value) && !in_array("User", $value)) {
      $value[] = "user";
    }
    return $value;
  }
}
