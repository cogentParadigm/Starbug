<?php
namespace Starbug\Core;

class StoreDatetimeHook extends QueryHook {
  public function validate($query, $key, $value, $column, $argument) {
    return (empty($value) || $value == "NULL") ? $value : date('Y-m-d H:i:s', strtotime($value));
  }
}
