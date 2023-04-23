<?php
namespace Starbug\Db\Query;

use Starbug\Db\DatabaseInterface;

interface BuilderFactoryInterface {
  public function create(DatabaseInterface $db);
}
