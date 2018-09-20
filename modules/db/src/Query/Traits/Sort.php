<?php
namespace Starbug\Db\Query\Traits;

trait Sort {
  protected $sort = [];

  public function addSort($column, $direction = 0) {
    $this->sort[$column] = $direction;
  }

  public function getSort() {
    return $this->sort;
  }
}
