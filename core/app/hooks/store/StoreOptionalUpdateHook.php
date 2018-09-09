<?php
namespace Starbug\Core;

class StoreOptionalUpdateHook extends QueryHook {
  function before_update($query, $key, $value, $column, $argument) {
    if (empty($value)) $query->exclude($key);
    return $value;
  }
}
