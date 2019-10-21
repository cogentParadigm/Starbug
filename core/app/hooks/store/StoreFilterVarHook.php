<?php
namespace Starbug\Core;

class StoreFilterVarHook extends QueryHook {
  public function validate($query, $key, $value, $column, $argument) {
    return (empty($value) ? $value : filter_var($value, $argument));
  }
}
