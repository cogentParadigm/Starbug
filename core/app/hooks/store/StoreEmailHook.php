<?php
namespace Starbug\Core;

class StoreEmailHook extends QueryHook {
  protected $models;
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function validate($query, $key, $value, $column, $argument) {
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
      $this->db->error("Please enter a valid email address.", $column, $query->model);
    }
    return $value;
  }
}
