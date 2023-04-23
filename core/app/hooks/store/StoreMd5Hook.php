<?php
namespace Starbug\Core;

use Starbug\Db\Query\ExecutorHook;

class StoreMd5Hook extends ExecutorHook {
  public function validate($query, $key, $value, $column, $argument) {
    return (empty($value) ? "" : md5($value));
  }
}
