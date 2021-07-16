<?php
namespace Starbug\Db;

use Starbug\Core\DatabaseInterface;

class RemoveCommand {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function run($argv) {
    $positional = [];
    $named = [];
    foreach ($argv as $i => $arg) {
      if (0 === strpos($arg, "-")) {
        $arg = ltrim($arg, "-");
        $parts = (false !== strpos($arg, "=")) ? explode("=", $arg, 2) : [$arg, true];
        $named[$parts[0]] = $parts[1];
      } else {
        $positional[] = $arg;
      }
    }
    $name = array_shift($positional);
    if (!empty($positional)) {
      $named["id"] = array_shift($positional);
    }
    $this->db->remove($name, $named);
  }
}
