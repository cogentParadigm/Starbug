<?php
namespace Starbug\Imports\Operation;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\Operation\Save;
use Starbug\Imports\Transform\Factory;

class SaveTransformer extends Save {
  protected $model = "imports_transformers";
  public function __construct(
    DatabaseInterface $db,
    Factory $transformers
  ) {
    $this->db = $db;
    $this->transformers = $transformers;
  }
  public function handle(array $data, BundleInterface $state): BundleInterface {
    $record = [];
    $values = [];
    $settings = $this->getSettings($data["type"]);
    foreach ($data as $column => $value) {
      if (in_array($column, $settings)) {
        $values[$column] = $value;
      } else {
        $record[$column] = $value;
      }
    }
    $state = parent::handle($record, $state);
    $id = $record["id"] ?? $this->db->getInsertId($this->model);
    $this->saveSettings($id, $values);
    return $state;
  }
  protected function getSettings($type) {
    $items = $this->transformers->getTransformers();
    $item = $items[$type];
    if (!empty($item["settings"])) {
      return array_column($item["settings"], "name");
    }
    return [];
  }
  protected function saveSettings($id, $values) {
    foreach ($values as $column => $value) {
      $conditions = [
        "imports_transformers_id" => $id,
        "name" => $column
      ];
      $upsert = $this->db->query("imports_transformers_settings")
        ->set($conditions + ["value" => $value]);
      $exists = $this->db->query("imports_transformers_settings")
        ->condition($conditions);
      if ($row = $exists->one()) {
        $upsert->condition("id", $row["id"])->update();
      } else {
        $upsert->insert();
      }
    }
  }
}
