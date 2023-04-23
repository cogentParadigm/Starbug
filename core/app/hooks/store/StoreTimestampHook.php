<?php
namespace Starbug\Core;

use Starbug\Db\Query\ExecutorHook;

class StoreTimestampHook extends ExecutorHook {
  public function emptyBeforeInsert($query, $column, $argument) {
    $query->set($column, date("Y-m-d H:i:s"));
  }
  public function emptyBeforeUpdate($query, $column, $argument) {
    if ($argument == "update") {
      $query->set($column, date("Y-m-d H:i:s"));
    }
  }
  public function beforeInsert($query, $key, $value, $column, $argument) {
    return date("Y-m-d H:i:s");
  }
  public function beforeUpdate($query, $key, $value, $column, $argument) {
    if ($argument == "insert") {
      $query->exclude($key);
    }
    return date("Y-m-d H:i:s");
  }
}
