<?php
namespace Starbug\Core;

class StoreAddslashesHook extends QueryHook {
  public function validate($query, $key, $value, $column, $argument) {
    return addslashes($value);
  }
}
