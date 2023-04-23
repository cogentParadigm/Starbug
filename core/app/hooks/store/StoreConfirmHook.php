<?php
namespace Starbug\Core;

use Starbug\Db\DatabaseInterface;
use Starbug\Db\Query\ExecutorHook;

class StoreConfirmHook extends ExecutorHook {
  protected $models;
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function validate($query, $key, $value, $column, $argument) {
    if ($query->hasValue($argument) && $value != $query->getValue($argument)) {
      $this->db->error("Your $column"." fields do not match", $column, $query->model);
    }
    $query->exclude($argument);
    return $value;
  }
}
