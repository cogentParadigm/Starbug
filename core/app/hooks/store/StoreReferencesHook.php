<?php
namespace Starbug\Core;

class StoreReferencesHook extends QueryHook {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function validate($query, $key, $value, $column, $argument) {
    $query->setMeta("{$column}.references.replace", $query->getUnvalidatedValue($key) === "");
    return $value;
  }
  public function store($query, $key, $value, $column, $argument) {
    $parts = explode(" ", $argument);
    $model = reset($parts);
    return ($query->getMeta("{$column}.references.replace") && !is_null($this->db->getInsertId($model))) ? $this->db->getInsertId($model) : $value;
  }
}
