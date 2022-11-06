<?php
namespace Starbug\Core;

class StoreOptionalUpdateHook extends QueryHook {
  public function beforeUpdate($query, $key, $value, $column, $argument) {
    if (empty($value)) {
      $query->exclude($key);
    }
    return $value;
  }
}
