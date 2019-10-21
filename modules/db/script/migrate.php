<?php
namespace Starbug\Db;

use Starbug\Db\Schema\SchemerInterface;

class MigrateCommand {
  public function __construct(SchemerInterface $schemer) {
    $this->schemer = $schemer;
  }
  public function run($argv) {
    $this->schemer->migrate();
  }
}
