<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\Query\ExecutorHook;

class StoreDatetimeHook extends ExecutorHook {
  public function validate($query, $key, $value, $column, $argument) {
    return (empty($value) || $value == "NULL") ? $value : date('Y-m-d H:i:s', strtotime($value));
  }
}
