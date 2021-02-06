<?php
namespace Starbug\Core\Routing\Traits;

trait Operations {
  protected $operations = [];
  public function setOperation($method, $operation) {
    $method = $this->normalizeMethod($method);
    $this->operations[$method] = $operation;
    return $this;
  }
  public function getOperation($method) {
    $method = $this->normalizeMethod($method);
    return $this->operations[$method];
  }
  public function getOperations() {
    return $this->operations;
  }
  public function hasOperation($method) {
    $method = $this->normalizeMethod($method);
    return isset($this->operations[$method]);
  }
  public function hasOperations() {
    return !empty($this->operations);
  }
  public function removeOperation($method) {
    $method = $this->normalizeMethod($method);
    unset($this->operations[$method]);
    return $this;
  }
  public function setOperations($operations) {
    $this->operations = [];
    foreach ($operations as $method => $operation) {
      $this->setOperation($method, $operation);
    }
    return $this;
  }
  public function onPost($operation) {
    return $this->setOperation("post", $operation);
  }
  public function onPut($operation) {
    return $this->setOperation("put", $operation);
  }
  public function onDelete($operation) {
    return $this->setOperation("delete", $operation);
  }
  protected function normalizeMethod($method) {
    return strtolower($method);
  }
}
