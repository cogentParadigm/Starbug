<?php
namespace Starbug\Db;

use Starbug\Core\ModelFactoryInterface;

class RemoveCommand {
  public function __construct(ModelFactoryInterface $models) {
    $this->models = $models;
  }
  public function run($argv) {
    $name = array_shift($argv);
    $id = array_shift($argv);
    $this->models->get($name)->remove($id);
  }
}
