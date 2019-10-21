<?php
namespace Starbug\Core;

class StoreLengthHook extends QueryHook {
  protected $models;
  public function __construct(ModelFactoryInterface $models) {
    $this->models = $models;
  }
  public function validate($query, $key, $value, $column, $argument) {
    $length = explode("-", $argument);
    if (!next($length)) $length = [0, $length[0]];
    $value_length = strlen($value);
    if (!($value_length >= $length[0] && $value_length <= $length[1])) $this->models->get($query->model)->error("This field must be between $length[0] and $length[1] characters long.", $column);
    return $value;
  }
}
