<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\DatabaseInterface;
use Starbug\Db\Query\ExecutorHook;

class StoreEmailHook extends ExecutorHook {
  protected $models;
  public function __construct(
    protected DatabaseInterface $db
  ) {
  }
  public function validate($query, $key, $value, $column, $argument) {
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
      $this->db->error("Please enter a valid email address.", $column, $query->model);
    }
    return $value;
  }
}
