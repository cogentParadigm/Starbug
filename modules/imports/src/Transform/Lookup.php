<?php
namespace Starbug\Imports\Transform;

use Starbug\Core\DatabaseInterface;
use Starbug\Db\Schema\SchemerInterface;

class Lookup extends AbstractTransformer {
  public function __construct(DatabaseInterface $db, SchemerInterface $schemer) {
    $this->db = $db;
    $this->schema = $schemer->getSchema();
  }
  public function transform($source, $dest, $options = []): array {
    if ($options["field"] == "id") {
      return $this->lookupId($dest, $options);
    }
    if (empty($dest[$options["field"]])) {
      return $dest;
    }
    $col = $this->schema->getColumn($options["model"], $options["field"]);
    if ($this->schema->hasTable($col["type"])) {
      return $this->lookupMultipleReference($dest, $options, $col["type"]);
    }
    if (!empty($col["references"])) {
      return $this->lookupSingleReference($dest, $options, $col["references"]);
    }
    return $dest;
  }
  public function getLabel($row): string {
    return "Lookup ".$row["field"]." by ".$row["by"];
  }
  protected function lookupId($dest, $options = []) {
    $keys = $options["by"];
    if (!is_array($keys)) {
      $keys = explode(",", $options["by"]);
    }
    $query = $this->db->query($options["model"]);
    foreach ($keys as $key) {
      $query->condition($options["model"].".".$key, $dest[$key] ?? "NULL");
    }
    $exists = $query->one();
    if ($exists) {
      $dest["id"] = $exists["id"];
    }
    return $dest;
  }
  protected function lookupMultipleReference($dest, $options, $target) {
    $items = $this->db->query($target)
    ->condition($options["by"], explode($options["delimiter"], $dest[$options["field"]]))->all();
    if (!empty($items)) {
      $dest[$options["field"]] = array_column($items, "id");
    }
    return $dest;
  }
  protected function lookupSingleReference($dest, $options, $reference) {
    $target = explode(" ", $reference)[0];
    $item = $this->db->query($target)
    ->condition($options["by"], $dest[$options["field"]])->one();
    if (!empty($item)) {
      $dest[$options["field"]] = $item["id"];
    }
    return $dest;
  }
}
