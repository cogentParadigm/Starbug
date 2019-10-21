<?php
namespace Starbug\Core;

class StoreMd5Hook extends QueryHook {
  public function validate($query, $key, $value, $column, $argument) {
    return (empty($value) ? "" : md5($value));
  }
}
