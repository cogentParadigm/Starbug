<?php
namespace Starbug\Core;

use Starbug\Db\DatabaseInterface;

class DbHelper {
  public function __construct(DatabaseInterface $db) {
    $this->target = $db;
  }
  public function helper() {
    return $this->target;
  }
}
