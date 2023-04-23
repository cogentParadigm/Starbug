<?php
namespace Starbug\Core;

use Starbug\Db\Query\ExecutorHook;

class StoreAddslashesHook extends ExecutorHook {
  public function validate($query, $key, $value, $column, $argument) {
    return addslashes($value);
  }
}
