<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\Query\ExecutorHook;

class StoreTimeHook extends ExecutorHook {
  public function validate($query, $key, $value, $column, $argument) {
    return (empty($value)) ? $value : date('H:i:s', strtotime($value));
  }
}
