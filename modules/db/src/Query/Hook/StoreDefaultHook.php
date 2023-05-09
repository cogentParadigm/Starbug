<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\Query\ExecutorHook;

class StoreDefaultHook extends ExecutorHook {
  public function emptyBeforeInsert($query, $column, $argument) {
    $query->set($column, $argument);
  }
  public function validate($query, $key, $value, $column, $argument) {
    return ($value === "") ? $argument : $value;
  }
}
