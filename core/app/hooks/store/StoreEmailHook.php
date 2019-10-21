<?php
namespace Starbug\Core;

class StoreEmailHook extends QueryHook {
  protected $models;
  public function __construct(ModelFactoryInterface $models) {
    $this->models = $models;
  }
  public function validate($query, $key, $value, $column, $argument) {
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) $this->models->get($query->model)->error("Please enter a valid email address.", $column);
    return $value;
  }
}
