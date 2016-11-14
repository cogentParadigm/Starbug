<?php
namespace Starbug\Doctrine\Schema;
use Starbug\Core\DatabaseInterface;
use Starbug\Db\Schema\AbstractMigration;
use Starbug\Db\Schema\SchemaInterface;
use Starbug\Core\Generator\Generator;
use Starbug\Core\Generator\Definitions\Model;
use Starbug\Core\Bundle;
class Migrator extends AbstractMigration {
	public function __construct(DatabaseInterface $db, SchemaInterface $schema, Generator $generator, Model $definition) {
		$this->db = $db;
		$this->schema = $schema;
		$this->generator = $generator;
		$this->definition = $definition;
	}
	public function migrate() {
		$conn = $this->db->getConnection();
		$sm = $conn->getSchemaManager();
		$from = $sm->createSchema();
		$to = new \Doctrine\DBAL\Schema\Schema();
		$comparator = new \Doctrine\DBAL\Schema\Comparator();
		$tables = $this->schema->getTables();
		foreach ($tables as $name => $table) {
			$t = $to->createTable($this->db->prefix($name));
			$columns = $table->getColumns();
			$primary = [];
			foreach ($columns as $column => $options) {
				if (!$this->schema->hasTable($options["type"])) {
					$t->addColumn($column, $this->getType($options), $this->getOptions($options));
					if (isset($options["key"]) && $options["key"] == "primary") {
						$primary[] = $column;
					}
					if (isset($options["references"])) {
						$parts = explode(" ", $options["references"]);
						$t->addForeignKeyConstraint($this->db->prefix(array_shift($parts)), [$column], $parts);
					}
					if (isset($options["index"])) {
						$t->addIndex([$column]);
					}
					if (isset($options["unique"]) && empty($options["unique"])) {
						$t->addUniqueIndex([$column]);
					}
				}
			}
			$t->setPrimaryKey($primary);
		}
		$diff = $comparator->compare($from, $to);
		$sql = $diff->toSaveSql($conn->getDatabasePlatform());
		if (empty($sql)) {
			echo "The Database already matches the schema.\n";
		} else {
			echo "Updating database..\n";
			foreach ($sql as $line) {
				$this->db->exec($line);
			}
		}
		echo "Generating models..\n";
		foreach ($tables as $name => $table) {
			$this->generator->generate($this->definition, ["model" => $name, "base" => true]);
		}
		$this->populate();
	}
	protected function getType($column) {
		$types = [
			"int" => "integer",
			"category" => "integer",
			"bool" => "boolean",
			"boolean" => "boolean",
			"string" => "string",
			"password" => "string",
			"text" => "text",
			"datetime" => "datetime",
			"date" => "date",
			"time" => "time",
			"json" => "json"
		];
		return $types[$column["type"]];
	}
	protected function getOptions($column) {
		$options = array();
		foreach (["default", "length"] as $key) {
			if (isset($column[$key])) $options[$key] = $column[$key];
		}
		if (isset($column["null"]) && $column["null"] !== false) {
			$options["notnull"] = false;
		}
		if (isset($column["auto_increment"])) {
			$options["autoincrement"] = $column["auto_increment"];
		}
		if ($column["type"] == "password") {
			$options["length"] = 100;
		}
		if ($column["type"] == "string" && empty($column["length"])) {
			$options["length"] = 64;
		}
		if ($column["type"] == "int" && empty($column["length"])) {
			$options["length"] = 11;
		}
		if (isset($options["default"]) && $options["default"] == "NULL") {
			$options["default"] = null;
		}
		return $options;
	}
	function populate() {
		$rows = $this->schema->getRows();
		foreach ($rows as $bundle) {
			$row = $bundle->get();
			$match = $this->db->query($row["table"])->conditions($row['keys'])->one();
			if (empty($match)) {
				$store = array_merge($row['keys'], $row['defaults']);
				foreach ($store as $k => $v) {
					if ($v instanceof Bundle) {
						$ref = $this->db->query($v->get("table"))->conditions($v->get("keys"))->one();
						$store[$k] = $ref["id"];
					}
				}
				fwrite(STDOUT, "Inserting ".$row["table"]." record...\n");
				$this->db->store($row["table"], $store);
			}
		}
	}
}
?>
