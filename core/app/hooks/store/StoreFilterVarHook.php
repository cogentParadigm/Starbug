<?php
namespace Starbug\Core;

use Starbug\Db\Query\ExecutorHook;

class StoreFilterVarHook extends ExecutorHook {
  public function validate($query, $key, $value, $column, $argument) {
    return (empty($value) ? $value : filter_var($value, $argument));
  }
}
