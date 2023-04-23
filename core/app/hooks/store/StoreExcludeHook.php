<?php
namespace Starbug\Core;

use Starbug\Db\Query\ExecutorHook;

class StoreExcludeHook extends ExecutorHook {
  public function insert($query, $key, $value, $column, $argument) {
    if ($argument == "insert") {
      $query->exclude($key);
    }
    return $value;
  }
  public function update($query, $key, $value, $column, $argument) {
    if ($argument == "update") {
      $query->exclude($key);
    }
    return $value;
  }
  public function store($query, $key, $value, $column, $argument) {
    if ($argument == "always") {
      $query->exclude($key);
    }
    return $value;
  }
}
