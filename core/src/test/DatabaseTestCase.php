<?php
namespace Starbug\Core;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\Operation\Composite;
use PHPunit\DbUnit\Operation\Factory;
use PDO;

abstract class DatabaseTestCase extends TestCase {

  use TestCaseTrait;

  protected $conn = null;
  protected $db = null;

  public function getSetUpOperation() {
    $cascadeTruncates = false; // If you want cascading truncates, false otherwise. If unsure choose false.
    return new Composite([
      new TruncateOperation($cascadeTruncates),
      Factory::INSERT()
    ]);
  }

  final public function getConnection() {
    global $container;
    if ($this->conn === null) {
      $config = $container->get("Starbug\Core\ConfigInterface");
      $name = $container->get("database_name");
      $params = $config->get("db/".$name);
      $pdo = new PDO('mysql:host='.$params['host'].';dbname='.$params['db'], $params['username'], $params['password']);
      $this->conn = $this->createDefaultDBConnection($pdo, $name);
    }
    if ($this->db === null) {
      $this->db = $container->get("Starbug\Core\DatabaseInterface");
    }
    return $this->conn;
  }
}
