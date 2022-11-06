<?php
namespace Starbug\Core;

class StoreSelectionHook extends QueryHook {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
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
