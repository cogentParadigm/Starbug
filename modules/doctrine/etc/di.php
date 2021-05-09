<?php
namespace Starbug\Doctrine;

use DI;

return [
  'Starbug\Core\DatabaseInterface' => DI\autowire('Starbug\Doctrine\Database')
    ->method('setTimeZone', DI\get('time_zone'))
    ->method('setDatabase', DI\get("databases.active")),
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Doctrine\Schema\Migrator')
  ])
];
