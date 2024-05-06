<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\DatabaseInterface;
use Starbug\Db\Query\ExecutorHook;

class StoreSelectionHook extends ExecutorHook {
  public function __construct(
    protected DatabaseInterface $db
  ) {
  }
  public function afterStore($query, $key, $value, $column, $argument) {
    if (empty($value)) {
      return;
    }
    $id = $query->getId();
    $conditions = ["`".$column."`" => 1];
    if (!empty($argument)) {
      $fields = explode(" ", $argument);
      $row = $this->db->query($query->model)->condition("id", $id)->one();
      foreach ($fields as $field) {
        $conditions[$field] = $row[$field];
      }
    }
    $this->db->query($query->model)->conditions($conditions)->condition("id", $id, "!=")->set($column, 0)->update();
  }
}
