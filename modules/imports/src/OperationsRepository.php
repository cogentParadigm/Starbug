<?php
namespace Starbug\Imports;

use Exception;
use Psr\Container\ContainerInterface;
use Starbug\Operation\OperationFactoryInterface;

class OperationsRepository {
  protected $operations = [];
  public function __construct(ContainerInterface $container, OperationFactoryInterface $factory) {
    $this->container = $container;
    $this->operations = $container->get("importer.operations");
    $this->factory = $factory;
  }
  public function getAvailableOperations($model = false) {
    $operations = $this->operations;
    if ($this->container->has("importer.operations.{$model}")) {
      $mods = $this->container->get("importer.operations.{$model}");
      foreach ($mods as $id => $config) {
        if (isset($operations[$id])) {
          $config += $operations[$id];
        }
        $operations[$id] = $config;
      }
    }
    return $operations;
  }
  public function getOperation($model, $name) {
    $operations = $this->getAvailableOperations($model);
    if (isset($operations[$name])) {
      return $this->factory->get($operations[$name]["class"]);
    }
    throw new Exception("Operation '{$name}' not found.");
  }
}
