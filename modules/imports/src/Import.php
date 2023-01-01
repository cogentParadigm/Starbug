<?php
namespace Starbug\Imports;

class Import {
  protected $model;
  protected $strategy;
  protected $strategyParameters = [];
  protected $operation;
  protected $operationParameters = [];
  protected $fields = [];
  protected $transformers = [];
  public function __construct($model, $operation = false, $operationParameters = []) {
    $this->setModel($model);
    if (false !== $operation) {
      $this->setOperation($operation, $operationParameters);
    }
  }
  public function setModel($model) {
    $this->model = $model;
  }
  public function getModel() {
    return $this->model;
  }
  public function setReadStrategy($strategy, $params = false) {
    $this->strategy = $strategy;
    if (false !== $params) {
      $this->strategyParameters = $params;
    }
  }
  public function getReadStrategy() {
    return $this->strategy;
  }
  public function getReadStrategyParameters() {
    return $this->strategyParameters;
  }
  public function setOperation($operation, $params = false) {
    $this->operation = $operation;
    if (false !== $params) {
      $this->operationParameters = $params;
    }
  }
  public function getOperation() {
    return $this->operation;
  }
  public function getOperationParameters() {
    return $this->operationParameters + ["model" => $this->model];
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