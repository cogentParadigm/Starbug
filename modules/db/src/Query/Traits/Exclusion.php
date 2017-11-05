<?php
namespace Starbug\Db\Query\Traits;

trait Exclusion {
  protected $exclusions = [];

  public function addExclusion($column) {
    $this->exclusions[$column] = true;
  }

  public function removeExclusion($column) {
    $this->exclusions[$column] = false;
  }

  public function isExcluded($column) {
    return isset($this->exclusions[$column]) && true == $this->exclusions[$column];
  }
}
