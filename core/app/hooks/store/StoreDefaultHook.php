<?php
namespace Starbug\Core;

class StoreDefaultHook extends QueryHook {
  public function emptyBeforeInsert($query, $column, $argument) {
    $query->set($column, $argument);
  }
  public function validate($query, $key, $value, $column, $argument) {
    return ($value === "") ? $argument : $value;
  }
}
