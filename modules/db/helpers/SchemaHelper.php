<?php
namespace Starbug\Db;

use Starbug\Db\Schema\SchemerInterface;

class SchemaHelper {
  public function __construct(SchemerInterface $schemer) {
    $this->target = $schemer->getSchema();
  }
  public function helper() {
    return $this->target;
  }
}
