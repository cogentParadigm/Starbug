<?php
namespace Starbug\Db;

use Starbug\Core\DatabaseInterface;

class DescribeCommand {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function run($argv) {
    $name = array_shift($argv);
    $records = $this->db->exec("DESCRIBE `".$this->db->prefix($name)."`")->fetchAll();
    if (!empty($records)) {
      $result = [];
      foreach ($records as $record) {
        $result[] = array_values($record);
      }
      $table = new \cli\Table();
      $table->setHeaders(array_keys($records[0]));
      $table->setRows($result);
      $table->display();
    }
  }
}
