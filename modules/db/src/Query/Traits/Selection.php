<?php
namespace Starbug\Db\Query\Traits;

use Exception;

trait Selection {

  protected $distinct = false;
  protected $selection = [];
  protected $forUpdate = false;

  public function getSelection() {
    return $this->selection;
  }

  public function addSelection($selection, $alias = false) {
    if ($selection instanceof QueryInterface && empty($alias)) {
      throw new Exception("You must specify an alias when adding a subquery as a selection.");
    }
    if (false === $alias) $alias = $selection;
    $this->selection[$alias] = $selection;
    return $alias;
  }

  public function removeSelection($alias = false) {
    if (false === $alias) {
      $this->selection = [];
    } elseif (isset($this->selection[$alias])) {
      unset($this->selection[$alias]);
    }
  }

  public function addSubquery($selection, $alias = false) {
    $query = $this->createSubquery();
    $query->addSelection($selection);
    $this->addSelection($query, $alias);
    return $query;
  }

  public function createSubquery() {
    return new static($this->prefix, $this->identifierQuoteCharacter);
  }

  public function setForUpdate($forUpdate = true) {
    $this->forUpdate = $forUpdate;
  }

  public function isForUpdate() {
    return $this->forUpdate;
  }
}
