<?php
namespace Starbug\Core;

class StoreTimeHook extends QueryHook {
  public function validate($query, $key, $value, $column, $argument) {
    return (empty($value)) ? $value : date('H:i:s', strtotime($value));
  }
}
