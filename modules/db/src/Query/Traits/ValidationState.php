<?php
namespace Starbug\Db\Query\Traits;

trait ValidationState {
  protected $validated = false;
  protected $raw = false;
  protected $errorsPreventSaving = true;

  public function isValidated() {
    return $this->validated;
  }

  public function isRaw() {
    return $this->raw;
  }

  public function errorsPreventSaving() {
    return $this->errorsPreventSaving;
  }

  public function setValidated($validated = true) {
    $this->validated = $validated;
  }

  public function setRaw($raw = true) {
    $this->raw = $raw;
  }

  public function setErrorsPreventSaving($preventSaving = true) {
    $this->errorsPreventSaving = $preventSaving;
  }
}
