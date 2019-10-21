<?php
namespace Starbug\Db\Query\Traits;

trait Mutation {
  public function setMode($mode) {
    parent::setMode($mode);
  }
  public function addSelection($selection, $alias = false) {
    return parent::addSelection($selection, $alias);
  }
  public function removeSelection($alias) {
    parent::removeSelection($alias);
  }
  public function addSubquery($selection, $alias = false) {
    return parent::addSubquery($selection, $alias);
  }
}
