<?php
namespace Starbug\Doctrine;

use function DI\autowire;
use function DI\get;
use function DI\add;
use DI;
use Starbug\Db\DatabaseInterface;

return [
  DatabaseInterface::class => autowire(Database::class)
    ->method('setTimeZone', get('time_zone'))
    ->method('setDatabase', get("databases.active")),
  'db.schema.migrations' => add([
    get('Starbug\Doctrine\Schema\Migrator')
  ])
];
