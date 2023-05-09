<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\Query\ExecutorHook;

class StoreOptionalUpdateHook extends ExecutorHook {
  public function beforeUpdate($query, $key, $value, $column, $argument) {
    if (empty($value)) {
      $query->exclude($key);
    }
    return $value;
  }
}
