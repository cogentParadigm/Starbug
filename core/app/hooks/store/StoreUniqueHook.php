<?php
namespace Starbug\Core;

class StoreUniqueHook extends QueryHook {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function validate($query, $key, $value, $column, $argument) {
    if ($value == "NULL") {
      return $value;
    }
    $argument = explode(" ", $argument);
    $existing = $this->db->query($query->model)->select("id")->select($column)->condition($column, $value);
    foreach ($argument as $c) {
      if (!empty($c) && $query->hasValue($c)) {
        $existing->condition($c, $query->getValue($c));
      }
    }
    $row = $existing->one();
    $id = $query->getId();
    if ($row && (empty($id) || $id != $row["id"])) {
      $this->db->error("That $column already exists.", $column, $query->model);
    }
    return $value;
  }
}
