<?php
namespace Starbug\Core;

class StoreTimeHook extends QueryHook {
  function validate($query, $key, $value, $column, $argument) {
    return (empty($value)) ? $value : date('H:i:s', strtotime($value));
  }
}
