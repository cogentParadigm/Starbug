<?php
namespace Starbug\Core;

use Starbug\Db\DatabaseInterface;
use Starbug\Db\Query\ExecutorHook;

class StoreRequiredHook extends ExecutorHook {
  protected $models;
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function emptyBeforeInsert($query, $column, $argument) {
    if ($argument == "insert") {
      $this->db->error("This field is required.", $column, $query->error);
    }
  }
  public function emptyBeforeUpdate($query, $column, $argument) {
    if ($argument == "update") {
      $this->db->error("This field is required.", $column, $query->model);
    }
  }
  public function emptyValidate($query, $column, $argument) {
    if ($argument == "always") {
      $this->db->error("This field is required.", $column, $query->model);
    }
  }
  public function store($query, $key, $value, $column, $argument) {
    if ($value === "" && !$query->isExcluded($key)) {
      $this->db->error("This field is required", $column, $query->model);
    }
    return $value;
  }
}
