<?php
namespace Starbug\Imports\Write;

use Exception;
use Starbug\Imports\Read\StrategyInterface as ReadStrategyInterface;

class FixtureStrategy extends DatabaseStrategy {
  public function run(ReadStrategyInterface $readStrategy, $options = []) {
    $this->readStrategy = $readStrategy;
    $this->options = $options;

    foreach ($readStrategy->getRows($options) as $data) {

      $table = $data["table"];
      $rows = $data["rows"];
      $this->db->query($table)->unsafeTruncate();
      $this->operation->configure(["model" => $table]);

      foreach ($rows as $row) {
        $this->record = $row;
        $this->operation->execute($row);

        if ($errors = $this->db->errors(true)) {
          $this->db->errors->set([]);
          throw new Exception(json_encode($errors, JSON_PRETTY_PRINT));
        }
      }
    }
  }
}