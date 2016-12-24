<?php
/**
 * @defgroup ModelTest
 * the base test class for models
 * @ingroup test
 */
namespace Starbug\Core;
/**
 * The Fixture class. Fixtures hold data sets used by the testing harness
 * @ingroup Fixture
 */
abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase {

	protected $conn = null;
	protected $db = null;

	public function getSetUpOperation() {
		$cascadeTruncates = false; // If you want cascading truncates, false otherwise. If unsure choose false.
		return new \PHPUnit_Extensions_Database_Operation_Composite(array(
			new TruncateOperation($cascadeTruncates),
			\PHPUnit_Extensions_Database_Operation_Factory::INSERT()
		));
	}

	final public function getConnection() {
		global $container;
		if ($this->conn === null) {
			$this->conn = $this->createDefaultDBConnection($container->get("databases.default"), $container->get("database_name"));
		}
		if ($this->db === null) {
			$this->db = $container->get("Starbug\Core\DatabaseInterface");
		}
		return $this->conn;
	}
}
