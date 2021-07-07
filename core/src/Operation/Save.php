<?php
namespace Starbug\Core\Operation;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Operation\Operation;

class Save extends Operation {
  protected $model;
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function setModel($model) {
    $this->model = $model;
  }
  public function configure($options = []) {
    if (!empty($options["model"])) {
      $this->model = $options["model"];
    }
  }
  public function handle(array $data, BundleInterface $state): BundleInterface {
    $this->db->store($this->model, $data);
    return $this->getErrorState($state);
  }
  protected function getErrorState(BundleInterface $state): BundleInterface {
    if ($this->db->errors($this->model)) {
      $errors = $this->db->errors($this->model, true);
      $state->set($errors);
    }
    return $state;
  }
}
