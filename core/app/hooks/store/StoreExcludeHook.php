<?php
namespace Starbug\Core;

class StoreExcludeHook extends QueryHook {
  function insert($query, $key, $value, $column, $argument) {
    if ($argument == "insert") $query->exclude($key);
    return $value;
  }
  function update($query, $key, $value, $column, $argument) {
    if ($argument == "update") $query->exclude($key);
    return $value;
  }
  function store($query, $key, $value, $column, $argument) {
    if ($argument == "always") $query->exclude($key);
    return $value;
  }
}
