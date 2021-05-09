<?php
namespace Starbug\Core;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\Operation\Composite;
use PHPunit\DbUnit\Operation\Factory;
use PDO;
use Starbug\Behat\DbUnit\DataSet\PrefixedMysqlXmlDataSet;

abstract class DatabaseTestCase extends TestCase {

  use TestCaseTrait;

  protected $conn = null;
  protected $db = null;

  protected function createMySQLXMLDataSet($xmlFile) {
    return new PrefixedMysqlXmlDataSet($xmlFile, $this->db->getPrefix());
  }

  public function getSetUpOperation() {
    return new Composite([
      Factory::TRUNCATE(true),
      Factory::INSERT()
    ]);
  }

  final public function getConnection() {
    global $container;
    if ($this->conn === null) {
      $name = $container->get("db");
      $params = $container->get("databases.".$name);
      $pdo = new PDO('mysql:host='.$params['host'].';dbname='.$params['db'], $params['username'], $params['password']);
      $this->conn = $this->createDefaultDBConnection($pdo, $name);
    }
    if ($this->db === null) {
      $this->db = $container->get("Starbug\Core\DatabaseInterface");
    }
    return $this->conn;
  }
}
