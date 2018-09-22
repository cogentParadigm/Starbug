<?php
namespace Starbug\Core;

class StoreOptionalUpdateHook extends QueryHook {
  function beforeUpdate($query, $key, $value, $column, $argument) {
    if (empty($value)) $query->exclude($key);
    return $value;
  }
}
