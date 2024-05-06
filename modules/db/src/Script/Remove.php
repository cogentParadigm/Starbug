<?php
namespace Starbug\Db\Script;

use Starbug\Db\DatabaseInterface;

class Remove {
  public function __construct(
    protected DatabaseInterface $db
  ) {
  }
  public function __invoke($positional, $named) {
    $name = array_shift($positional);
    if (!empty($positional)) {
      $named["id"] = array_shift($positional);
    }
    $this->db->remove($name, $named);
  }
}
