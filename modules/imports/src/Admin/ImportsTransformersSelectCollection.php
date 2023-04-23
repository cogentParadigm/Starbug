<?php
namespace Starbug\Imports\Admin;

use Starbug\Db\DatabaseInterface;
use Starbug\Core\SelectCollection;
use Starbug\Db\Schema\SchemerInterface;
use Starbug\Imports\Transform\Factory;

class ImportsTransformersSelectCollection extends SelectCollection {
  protected $model = "imports_transformers";
  public function __construct(
    DatabaseInterface $db,
    SchemerInterface $schemer,
    Factory $transformers
  ) {
    parent::__construct($db, $schemer);
    $this->transformers = $transformers;
  }
  public function build($query, $ops) {
    $query = parent::build($query, $ops);
    $query->removeSelection();
    $query->select("imports_transformers.*");
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as $idx => $row) {
      $settings = $this->db->query("imports_transformers_settings")
        ->condition("imports_transformers_id", $row["id"])
        ->all();
      foreach ($settings as $setting) {
        $row[$setting["name"]] = $setting["value"];
      }
      $rows[$idx] = [
        "id" => $row["id"],
        "label" => $this->transformers->get($row["type"])->getLabel($row)
      ];
    }
    return parent::filterRows($rows);
  }
}
