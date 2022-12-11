<?php
namespace Starbug\Db;

use Starbug\Core\DatabaseInterface;

class StoreCommand {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function run($argv) {
    $name = array_shift($argv);
    $params = $this->parse($argv);
    $this->db->store($name, $params);
    if (!$this->db->errors()) {
      $id = $params['id'] ?? $this->db->getInsertId($name);
      $records = $this->db->query($name)->condition($name.".id", $id)->all();
      $result = [];
      foreach ($records as $record) {
        $result[] = array_values($record);
      }
      $table = new \cli\Table();
      $table->setHeaders(array_keys($records[0]));
      $table->setRows($result);
      $table->display();
    } else {
      $errors = $this->db->errors(true);
      $result = [];
      foreach ($errors as $col => $arr) {
        foreach ($arr as $e => $m) {
          $result[] = [$col, $m];
        }
      }
      $table = new \cli\Table();
      $table->setHeaders(["field", "message"]);
      $table->setRows($result);
      $table->display();
    }
  }
  public function parse($args) {
    $params = [];
    foreach ($args as $arg) {
      $arg = explode(":", $arg, 2);
      $params[$arg[0]] = $arg[1];
    }
    return $params;
  }
}
