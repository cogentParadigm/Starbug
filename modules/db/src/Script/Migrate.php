<?php
namespace Starbug\Db\Script;

use Starbug\Db\Schema\SchemerInterface;

class Migrate {
  public function __construct(SchemerInterface $schemer) {
    $this->schemer = $schemer;
  }
  public function __invoke() {
    $this->schemer->migrate();
  }
}
