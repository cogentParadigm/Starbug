<?php
namespace Starbug\Db\Script;

use Starbug\Db\Schema\SchemerInterface;

class Migrate {
  public function __construct(
    protected SchemerInterface $schemer
  ) {
  }
  public function __invoke() {
    $this->schemer->migrate();
  }
}
