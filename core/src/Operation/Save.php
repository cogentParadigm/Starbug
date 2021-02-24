<?php
namespace Starbug\Core\Operation;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Operation\Operation;

class Save extends Operation {
  protected $model;
  public function __construct(ModelFactoryInterface $models) {
    $this->models = $models;
  }
  public function setModel($model) {
    $this->model = $model;
  }
  protected function getModel() {
    return $this->models->get($this->model);
  }
  public function configure($options = []) {
    if (!empty($options["model"])) {
      $this->model = $options["model"];
    }
  }
  public function handle(BundleInterface $data, BundleInterface $state): BundleInterface {
    $this->create($data->get());
    return $this->getErrorState($state);
  }
  protected function getErrorState(BundleInterface $state): BundleInterface {
    if ($this->models->get($this->model)->db->errors()) {
      $errors = $this->models->get($this->model)->errors("", true);
      $state->set($errors);
    }
    return $state;
  }

  public function __call($name, $arguments) {
    return call_user_func_array([$this->getModel(), $name], $arguments);
  }

  public function __get($name) {
    return $this->getModel()->$name;
  }
}
