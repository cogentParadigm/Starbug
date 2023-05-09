<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Core\InputFilterInterface;
use Starbug\Core\MacroInterface;
use Starbug\Db\DatabaseInterface;
use Starbug\Db\Query\ExecutorHook;
use Starbug\Db\Schema\SchemaInterface;

class StoreSlugHook extends ExecutorHook {
  public function __construct(DatabaseInterface $db, MacroInterface $macro, InputFilterInterface $filter, SchemaInterface $schema) {
    $this->db = $db;
    $this->macro = $macro;
    $this->filter = $filter;
    $this->schema = $schema;
  }
  public function emptyBeforeInsert($query, $column, $argument) {
    $query->set($column, $this->validate($query, $column, "", $column, $argument));
  }
  public function validate($query, $key, $value, $column, $argument) {
    if (empty($value) && $query->hasValue($argument)) {
      $value = $query->getValue($argument);

      $value = strtolower(str_replace(" ", "-", $this->filter->normalize($value)));

      $field = $this->schema->getColumn($query->model, $column);
      if (!empty($field["pattern"])) {
        $pattern = $field["pattern"];
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
    $field = $this->schema->getColumn($query->model, $column);
    if (!empty($field["unique"])) {
      $parts = explode(" ", $field["unique"]);
      foreach ($parts as $c) {
        if (!empty($c) && $query->hasValue($c)) {
          $exists->condition($c, $query->getValue($c));
        }
      }
    }
    return $exists;
  }
}
