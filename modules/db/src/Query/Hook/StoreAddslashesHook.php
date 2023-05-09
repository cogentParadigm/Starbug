<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\Query\ExecutorHook;

class StoreAddslashesHook extends ExecutorHook {
  public function validate($query, $key, $value, $column, $argument) {
    return addslashes($value);
  }
}
