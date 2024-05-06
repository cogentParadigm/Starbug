<?php
namespace Starbug\Imports\Operation;

use Starbug\Db\DatabaseInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Core\Operation\Save;
use Starbug\Imports\Import;
use Starbug\Imports\Importer;
use Starbug\Imports\OperationsRepository;
use Starbug\Imports\Read\TabularSpreadsheetStrategy;
use Starbug\Imports\Write\DatabaseStrategy;

class Run extends Save {
  public function __construct(
    protected DatabaseInterface $db,
    protected Importer $importer,
    protected OperationsRepository $operations
  ) {
  }
  public function handle(array $data, BundleInterface $state): BundleInterface {
    // First, retrieve data from database.
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
    // Second, construct import from data and run
    $config = new Import($import["model"]);
    $config->setReadStrategy(TabularSpreadsheetStrategy::class, [
      "files_id" => $import["source"]
    ]);
    $config->setWriteStrategy(DatabaseStrategy::class, [
      "operation" => $this->operations->getOperation($import["model"], $import["operation"])
    ]);
    $config->setFields($fields);
    $config->setTransformers($transformers);
    $this->importer->run($config);
    return $this->getErrorState($state);
  }
}
