<?php
namespace Starbug\Db\Script;

use Starbug\Core\DatabaseInterface;

class Remove {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function __invoke($positional, $named) {
    $name = array_shift($positional);
    if (!empty($positional)) {
      $named["id"] = array_shift($positional);
    }
    $this->db->remove($name, $named);
  }
}
