<?php
namespace Starbug\Doctrine;

use function DI\autowire;
use function DI\get;
use function DI\add;
use DI;

return [
  'Starbug\Core\DatabaseInterface' => autowire('Starbug\Doctrine\Database')
    ->method('setTimeZone', get('time_zone'))
    ->method('setDatabase', get("databases.active")),
  'db.schema.migrations' => add([
    get('Starbug\Doctrine\Schema\Migrator')
  ])
];
