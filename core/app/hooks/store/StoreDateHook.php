<?php
namespace Starbug\Core;

class StoreDateHook extends QueryHook {
  public function validate($query, $key, $value, $column, $argument) {
    return (empty($value)) ? $value : date('Y-m-d', strtotime($value));
  }
}
