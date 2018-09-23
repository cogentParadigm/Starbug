<?php
namespace Starbug\Core;

class StoreConfirmHook extends QueryHook {
  protected $models;
  public function __construct(ModelFactoryInterface $models) {
    $this->models = $models;
  }
  public function validate($query, $key, $value, $column, $argument) {
    if ($query->hasValue($argument) && $value != $query->getValue($argument)) {
      $this->models->get($query->model)->error("Your $column"." fields do not match", $column);
    }
    $query->exclude($argument);
    return $value;
  }
}
