<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\Query\ExecutorHook;

class StoreMd5Hook extends ExecutorHook {
  public function validate($query, $key, $value, $column, $argument) {
    return (empty($value) ? "" : md5($value));
  }
}
