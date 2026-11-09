<?php
namespace Starbug\Db\Testing\Traits;

use ReflectionClass;
use PHPUnit\Framework\TestCase;
use Starbug\Db\DatabaseInterface;
use Starbug\Db\Operation\Migrate;
use Starbug\Importer\Import;
use Starbug\Importer\Importer;
use Starbug\Db\Importer\Read\YamlFixtureStrategy;
use Starbug\Db\Importer\Write\FixtureStrategy;
use Starbug\Testing\Attribute\Fixture;

/**
 * Database access and fixture loading for test cases.
 *
 * Provides lazy access to the database, importer, and migrate operation,
 * along with fixture scanning and loading via #[Fixture] attributes.
 *
 * Use `DatabaseTestCase` for the standard setup wiring (#[Bind]/#[Fixture]
 * resolution and fixture import before each test).
 */
trait Database {

  use DatabaseAssertions;

  protected $importer;
  protected $operation;
  protected DatabaseInterface $db;

  /**
   * Clear database errors and load fixture datasets.
   *
   * Call this in setUp() before test logic.
   */
  protected function databaseSetup(): void {
    $this->getDatabase()->errors->set([]);
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
    $reflection = new ReflectionClass($this);
    $imports = [];
    do {
      $attributes = $reflection->getAttributes(Fixture::class);
      foreach ($attributes as $attribute) {
        $fixture = $attribute->newInstance();
        if ($fixture->type === 'yaml') {
          $imports[] = $this->createYamlDataSet($fixture->path);
        }
      }
      $reflection = $reflection->getParentClass();
    } while ($reflection && $reflection->getName() !== TestCase::class);

    return empty($imports) ? false : array_reverse($imports);
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

  protected function getDatabase() {
    if (empty($this->db)) {
      global $container;
      $this->db = $container->get(DatabaseInterface::class);
    }
    return $this->db;
  }
}
