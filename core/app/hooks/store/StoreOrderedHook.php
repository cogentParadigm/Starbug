<?php
namespace Starbug\Core;

class StoreOrderedHook extends QueryHook {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function setConditions($query, $column, $argument, $value = "insert") {
    if (!$query->hasMeta("{$column}.ordered.conditions")) {
      $conditions = [];
      if (!empty($argument)) {
        $fields = explode(" ", $argument);
        if ($value === "insert") {
          foreach ($fields as $field) if ($query->hasValue($field)) $conditions[$query->model.".".$field] = $query->getValue($field);
        } else {
          $id = $query->getId();
          $row = $this->db->query($query->model)->select($query->model.".*")->condition("id", $id)->one();
          $same_level = true;
          foreach ($fields as $field) {
            if (is_null($row[$field])) $row[$field] = "NULL";
            $conditions[$query->model.".".$field] = $row[$field];
            if (isset($query->fields[$field]) && $query->fields[$field] != $row[$field]) $same_level = false;
          }
          if ($same_level) $query->setMeta("{$column}.ordered.increment", ($row[$column] < $value) ? -1 : 1);
        }
      } elseif ($value != "insert") {
        $id = $query->getId();
        $row = $this->db->query($query->model)->select($query->model.".*")->condition("id", $id)->one();
        $query->setMeta("{$column}.ordered.increment", ($row[$column] < $value) ? -1 : 1);
      }
      $query->setMeta("{$column}.ordered.conditions", $conditions);
    }
  }
  public function emptyBeforeInsert($query, $column, $argument) {
    $query->set($column, $this->insert($query, $column, "", $column, $argument));
  }
  public function insert($query, $key, $value, $column, $argument) {
    $this->setConditions($query, $column, $argument, "insert");
    if (!empty($value) && is_numeric($value)) $query->setMeta("{$column}.ordered.value", $value);
    $highest = $this->db->query($query->model)
      ->select("MAX(".$query->model.".$column) as highest")
      ->conditions($query->getMeta("{$column}.ordered.conditions"))
      ->condition($query->model.".deleted", "0")->one();
    return $highest['highest']+1;
  }
  public function update($query, $key, $value, $column, $argument) {
    $this->setConditions($query, $column, $argument, $value);
    return $value;
  }
  public function afterStore($query, $key, $value, $column, $argument) {
    if ($query->hasMeta("{$column}.ordered.value")) $value = $query->getMeta("{$column}.ordered.value");
    if (empty($value)) return;
    $select = ["id", $column];
    if (!empty($argument)) $select = array_merge($select);
    $id = $query->getId();
    $row = ["id" => $id];
    $ids = [$row['id']];
    while (!empty($row)) {
      $this->db->query($query->model)->condition("id", $row['id'])->set($column, $value)->raw()->update();
      $row = $this->db->query($query->model)
        ->select($select, $query->model)
        ->select(array_keys($query->getMeta("{$column}.ordered.conditions")))
        ->conditions($query->getMeta("{$column}.ordered.conditions"))
        ->condition($query->model.".id", $ids, "!=")
        ->condition($query->model.".deleted", "0")
        ->condition($query->model.".".$column, $value)->one();
      if (!empty($row)) $ids[] = $row['id'];
      $value += $query->getMeta("{$column}.ordered.increment", 1);
    }
    $query->removeMeta("{$column}.ordered.conditions");
    $query->removeMeta("{$column}.ordered.value");
  }
}
