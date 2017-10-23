<?php
namespace Starbug\Db\Query\Traits;

trait ValidationState {
  protected $dirty = true;
  protected $raw = false;
  protected $errorsPreventSaving = true;

  function isDirty() {
    return $this->dirty;
  }

  public function isRaw() {
    return $this->raw;
  }

  public function errorsPreventSaving() {
    return $this->errorsPreventSaving;
  }

  function setDirty($dirty = true) {
    $this->dirty = $dirty;
  }

  public function setRaw($raw = true) {
    $this->raw = $raw;
  }

  public function setErrorsPreventSaving($preventSaving = true) {
    $this->errorsPreventSaving = $preventSaving;
  }
}
