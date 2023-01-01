<?php
namespace Starbug\Imports;

use Psr\Container\ContainerInterface;

class ImporterFactory {
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }
  public function create($model, $operation, $keys = []) {
    return $this->container->make(Importer::class, [
      "model" => $model,
      "operation" => $operation,
      "keys" => $keys
    ]);
  }
}
