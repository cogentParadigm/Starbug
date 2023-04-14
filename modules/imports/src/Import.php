<?php
namespace Starbug\Imports;

class Import {
  protected $model;
  protected $readStrategy;
  protected $readStrategyParameters = [];
  protected $writeStrategy;
  protected $writeStrategyParameters = [];
  protected $fields = [];
  protected $transformers = [];
  public function __construct($model) {
    $this->setModel($model);
  }
  public function setModel($model) {
    $this->model = $model;
  }
  public function getModel() {
    return $this->model;
  }
  public function setReadStrategy($strategy, $params = false) {
    $this->readStrategy = $strategy;
    if (false !== $params) {
      $this->readStrategyParameters = $params;
    }
  }
  public function getReadStrategy() {
    return $this->readStrategy;
  }
  public function getReadStrategyParameters() {
    return $this->readStrategyParameters;
  }
  public function setWriteStrategy($strategy, $params = false) {
    $this->writeStrategy = $strategy;
    if (false !== $params) {
      $this->writeStrategyParameters = $params;
    }
  }
  public function getWriteStrategy() {
    return $this->writeStrategy;
  }
  public function getWriteStrategyParameters() {
    return $this->writeStrategyParameters;
  }
  public function setFields($fields) {
    $this->fields = $fields;
  }
  public function getFields() {
    return $this->fields;
  }
  public function setTransformers($transformers) {
    $this->transformers = $transformers;
  }
  public function addTransformer($transformer, $params = []) {
    $this->transformers[] = compact("transformer", "params");
  }
  public function getTransformers() {
    return $this->transformers;
  }
}
