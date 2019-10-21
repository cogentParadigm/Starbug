<?php
namespace Starbug\Core;

class StoreSlugHook extends QueryHook {
  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models, MacroInterface $macro, InputFilterInterface $filter) {
    $this->db = $db;
    $this->macro = $macro;
    $this->models = $models;
    $this->filter = $filter;
  }
  public function emptyBeforeInsert($query, $column, $argument) {
    $query->set($column, $this->validate($query, $column, "", $column, $argument));
  }
  public function validate($query, $key, $value, $column, $argument) {
    if (empty($value) && $query->hasValue($argument)) {
      $value = $query->getValue($argument);

      $value = strtolower(str_replace(" ", "-", $this->filter->normalize($value)));

      if (!empty($this->models->get($query->model)->hooks[$column]["pattern"])) {
        $pattern = $this->models->get($query->model)->hooks[$column]["pattern"];
        $data = [$query->model => array_merge($query->fields, [$column => $value])];
        $value = $this->macro->replace($pattern, $data);
      }

      $base = $value;
      $exists = $this->exists($query, $column, $value);
      $count = 2;
      while ($exists->one()) {
        $value = $base."-".$count;
        $exists = $this->exists($query, $column, $value);
        $count++;
      }
    }
    return $value;
  }

  public function exists($query, $column, $value) {
    $exists = $this->db->query($query->model)->condition($query->model.".".$column, $value);
    $id = 0;
    if ($query->mode == "update") {
      $id = $query->getId();
      $exists->condition($query->model.".id", $id, "!=");
    }
    if (!empty($this->models->get($query->model)->hooks[$column]["unique"])) {
      $parts = explode(" ", $this->models->get($query->model)->hooks[$column]["unique"]);
      foreach ($parts as $c) if (!empty($c)) $exists->condition($c, $query->fields[$c]);
    }
    return $exists;
  }
}
