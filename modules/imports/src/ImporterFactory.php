<?php
namespace Starbug\Imports;

use DI\FactoryInterface;
use Starbug\Importer\Importer;

class ImporterFactory {
  public function __construct(
    protected FactoryInterface $container
  ) {
  }
  public function create($model, $operation, $keys = []) {
    return $this->container->make(Importer::class, [
      "model" => $model,
      "operation" => $operation,
      "keys" => $keys
    ]);
  }
}
