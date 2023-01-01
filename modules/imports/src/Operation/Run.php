<?php
namespace Starbug\Imports\Operation;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\Operation\Save;
use Starbug\Imports\Import;
use Starbug\Imports\Importer;
use Starbug\Imports\Read\TabularSpreadsheetStrategy;

class Run extends Save {
  public function __construct(
    DatabaseInterface $db,
    Importer $importer
  ) {
    $this->db = $db;
    $this->importer = $importer;
  }
  public function handle(array $data, BundleInterface $state): BundleInterface {
    $import = $this->db->query("imports")->condition("id", $data["id"])->one();
    $fields = $this->db->query("imports_fields")
      ->condition("imports_id", $import["id"])
      ->sort("position")
      ->all();
    $keys = [];
    foreach ($fields as $field) {
      if ($field["update_key"]) {
        $keys[] = $field["destination"];
      }
    }
    $transformers = $this->db->query("imports_transformers")
      ->condition("imports_id", $import["id"])
      ->sort("position")
      ->all();
    foreach ($transformers as $idx => $row) {
      $settings = $this->db->query("imports_transformers_settings")
        ->condition("imports_transformers_id", $row["id"])
        ->all();
      foreach ($settings as $setting) {
        $row[$setting["name"]] = $setting["value"];
      }
      $transformers[$idx] = $row;
    }
    if (!empty($keys)) {
      $transformers[] = ["type" => "lookup", "field" => "id", "by" => $keys];
    }
    $config = new Import($import["model"], $import["operation"]);
    $config->setReadStrategy(TabularSpreadsheetStrategy::class, [
      "files_id" => $import["source"]
    ]);
    $config->setFields($fields);
    $config->setTransformers($transformers);
    $this->importer->run($config);
    return $this->getErrorState($state);
  }
}
