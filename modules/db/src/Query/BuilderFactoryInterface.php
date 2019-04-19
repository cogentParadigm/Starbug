<?php
namespace Starbug\Db\Query;

use Starbug\Core\DatabaseInterface;

interface BuilderFactoryInterface {
  public function create(DatabaseInterface $db);
}
