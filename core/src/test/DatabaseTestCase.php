<?php
namespace Starbug\Core;

use PHPUnit\Framework\TestCase;
use Starbug\Db\Operation\Migrate;
use Starbug\Imports\Import;
use Starbug\Imports\Importer;
use Starbug\Imports\Read\YamlFixtureStrategy;
use Starbug\Imports\Write\FixtureStrategy;

abstract class DatabaseTestCase extends TestCase {

  protected $importer;
  protected $operation;

  protected function setUp(): void {
    parent::setUp();
    if ($imports = $this->getDataSets()) {
      foreach ($imports as $import) {
        $this->getImporter()->run($import);
      }
    }

  }

  protected function getImporter() {
    if (empty($this->importer)) {
      global $container;
      $this->importer = $container->get(Importer::class);
    }
    return $this->importer;
  }

  protected function getDataSets() {
    return false;
  }

  protected function createYamlDataSet($ymlFile) {
    $import = new Import(false);
    $import->setReadStrategy(YamlFixtureStrategy::class, ["path" => $ymlFile]);
    $import->setWriteStrategy(FixtureStrategy::class, ["operation" => $this->getOperation()]);
    return $import;
  }

  protected function getOperation() {
    if (empty($this->operation)) {
      global $container;
      $this->operation = $container->get(Migrate::class);
    }
    return $this->operation;
  }
}
