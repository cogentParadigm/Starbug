<?php
namespace Starbug\Core;

use PDO;

abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase {

  protected $conn = null;
  protected $db = null;

  public function getSetUpOperation() {
    $cascadeTruncates = false; // If you want cascading truncates, false otherwise. If unsure choose false.
    return new \PHPUnit_Extensions_Database_Operation_Composite([
      new TruncateOperation($cascadeTruncates),
      \PHPUnit_Extensions_Database_Operation_Factory::INSERT()
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
