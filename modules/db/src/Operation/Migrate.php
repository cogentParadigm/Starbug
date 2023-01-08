<?php
namespace Starbug\Db\Operation;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\Operation\Save;

class Migrate extends Save {
  public function handle(array $record, BundleInterface $state): BundleInterface {
    $exists = false;
    if (!empty($record["id"])) {
      $exists = $this->db->query($this->model)->condition("id", $record["id"])->one();
    }
    $upsert = $this->db->query($this->model)
      ->set($record)
      ->addTag("keep_timestamps")
      ->addTag("keep_owner");
    if ($exists) {
      $upsert->condition("id", $record["id"])->update();
    } else {
      $upsert->insert();
    }
    return $this->getErrorState($state);
  }
}
