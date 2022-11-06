<?php
namespace Starbug\Db\Query\Traits;

trait Set {
  protected $values = [];
  protected $unvalidatedValues = [];

  public function setValue($field, $value) {
    $this->values[$field] = $value;
  }

  public function getValue($field) {
    return isset($this->values[$field]) ? $this->values[$field] : null;
  }

  public function hasValue($field) {
    return isset($this->values[$field]);
  }

  public function getValues() {
    return $this->values;
  }

  public function setValues(array $values) {
    $this->values = $values;
  }

  public function beginValidation() {
    $this->unvalidatedValues = $this->values;
  }

  public function getUnvalidatedValue($field) {
    if (isset($this->unvalidatedValues[$field])) {
      return $this->unvalidatedValues[$field];
    }
    return $this->getValue($field);
  }
}
