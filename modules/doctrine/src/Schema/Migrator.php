<?php
namespace Starbug\Doctrine\Schema;

use Starbug\Db\DatabaseInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Comparator;
use Starbug\Db\Schema\AbstractMigration;
use Starbug\Db\Schema\SchemaInterface;
use Starbug\Core\Generator\Generator;
use Starbug\Bundle\BundleInterface;

class Migrator extends AbstractMigration {
  public function __construct(
    protected DatabaseInterface $db,
    protected SchemaInterface $schema,
    protected Generator $generator
  ) {
    $this->db = $db;
    $this->schema = $schema;
    $this->generator = $generator;
  }
  public function migrate() {
    $conn = $this->db->getConnection();
    $sm = $conn->getSchemaManager();
    $from = $sm->createSchema();
    $to = new Schema();
    $comparator = new Comparator();
    $tables = $this->schema->getTables();
    foreach ($tables as $name => $table) {
      $t = $to->createTable($this->db->prefix($name));
      $columns = $table->getColumns();
      $indexes = $table->getIndexes();
      $primary = [];
      foreach ($columns as $column => $options) {
        if (!$this->schema->hasTable($options["type"]) && $options["type"] != "virtual") {
          $t->addColumn($column, $this->getType($options), $this->getOptions($options));
          if (isset($options["key"]) && $options["key"] == "primary") {
            $primary[] = $column;
          }
          if (isset($options["references"])) {
            $parts = explode(" ", $options["references"]);
            $fkOptions = [];
            foreach (["update", "delete"] as $option) {
              if (!empty($options[$option])) {
                $fkOptions["on".ucwords($option)] = $options[$option];
              }
            }
            $t->addForeignKeyConstraint($this->db->prefix(array_shift($parts)), [$column], $parts, $fkOptions);
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
      foreach ($indexes as $index) {
        if (!empty($index["unique"])) {
          $t->addUniqueIndex($index["columns"]);
        } else {
          $t->addIndex($index["columns"]);
        }
      }
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
    $this->populate();
  }
  protected function getType($column) {
    $types = [
      "int" => "integer",
      "decimal" => "decimal",
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
    return isset($types[$column["type"]]) ? $types[$column["type"]] : "integer";
  }
  protected function getOptions($column) {
    $options = [];
    foreach (["default", "length"] as $key) {
      if (isset($column[$key])) {
        $options[$key] = $column[$key];
      }
    }
    if (isset($column["null"]) && $column["null"] !== false) {
      $options["notnull"] = false;
    }
    if (isset($column["auto_increment"])) {
      $options["autoincrement"] = $column["auto_increment"];
    }
    if ($column["type"] == "password") {
      $options["length"] = 255;
    }
    if ($column["type"] == "string" && empty($column["length"])) {
      $options["length"] = 64;
    }
    if ($column["type"] == "int" && empty($column["length"])) {
      $options["length"] = 11;
    }
    if ($column["type"] == "decimal") {
      if (!empty($column["precision"])) {
        $options["precision"] = $column["precision"];
      }
      if (!empty($column["scale"])) {
        $options["scale"] = $column["scale"];
      }
    }
    if (isset($options["default"]) && $options["default"] == "NULL") {
      $options["default"] = null;
    }
    return $options;
  }
  public function populate() {
    $rows = $this->schema->getRows();
    foreach ($rows as $bundle) {
      $row = $bundle->get();
      $row["keys"] = $this->expandBundles($row["keys"]);
      $row["defaults"] = $this->expandBundles($row["defaults"]);
      $match = $this->db->query($row["table"])->conditions($row['keys'])->one();
      if (empty($match)) {
        $store = array_merge($row['keys'], $row['defaults']);
        fwrite(STDOUT, "Inserting ".$row["table"]." record...\n");
        $this->db->store($row["table"], $store);
      }
    }
  }
  protected function expandBundles($values) {
    foreach ($values as $k => $v) {
      if ($v instanceof BundleInterface) {
        $ref = $this->db->query($v->get("table"))->conditions($v->get("keys"))->one();
        if ($ref) {
          $values[$k] = $ref["id"];
        }
      }
    }
    return $values;
  }
}
