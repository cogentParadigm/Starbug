<?php
namespace Starbug\Core;

class StoreRequiredHook extends QueryHook {
  protected $models;
  public function __construct(ModelFactoryInterface $models) {
    $this->models = $models;
  }
  public function emptyBeforeInsert($query, $column, $argument) {
    if ($argument == "insert") $this->models->get($query->model)->error("This field is required.", $column);
  }
  public function emptyBeforeUpdate($query, $column, $argument) {
    if ($argument == "update") $this->models->get($query->model)->error("This field is required.", $column);
  }
  public function emptyValidate($query, $column, $argument) {
    if ($argument == "always") $this->models->get($query->model)->error("This field is required.", $column);
  }
  public function store($query, $key, $value, $column, $argument) {
    if ($value === "" && !$query->isExcluded($key)) $this->models->get($query->model)->error("This field is required", $column);
    return $value;
  }
}
