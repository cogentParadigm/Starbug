<?php
namespace Starbug\Db\Script;

use cli\Table;
use Starbug\Core\DatabaseInterface;

class Describe {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function __invoke($argv) {
    $name = array_shift($argv);
    $records = $this->db->exec("DESCRIBE `".$this->db->prefix($name)."`")->fetchAll();
    if (!empty($records)) {
      $result = [];
      foreach ($records as $record) {
        $result[] = array_values($record);
      }
      $table = new Table();
      $table->setHeaders(array_keys($records[0]));
      $table->setRows($result);
      $table->display();
    }
  }
}
