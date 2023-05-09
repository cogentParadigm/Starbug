<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\Query\ExecutorHook;

class StoreDateHook extends ExecutorHook {
  public function validate($query, $key, $value, $column, $argument) {
    return (empty($value) || $value == "NULL") ? $value : date('Y-m-d', strtotime($value));
  }
}
