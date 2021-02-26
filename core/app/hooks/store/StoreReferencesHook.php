<?php
namespace Starbug\Core;

class StoreReferencesHook extends QueryHook {
  protected $replace = false;
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function validate($query, $key, $value, $column, $argument) {
    if ($query->getUnvalidatedValue($key) === "") {
      $this->replace = true;
    }
    return $value;
  }
  public function store($query, $key, $value, $column, $argument) {
    $parts = explode(" ", $argument);
    $model = reset($parts);
    return ($this->replace && !is_null($this->db->getInsertId($model))) ? $this->db->getInsertId($model) : $value;
  }
}
