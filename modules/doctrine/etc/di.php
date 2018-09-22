<?php
return [
  'Starbug\Core\DatabaseInterface' => DI\object('Starbug\Doctrine\Database')
    ->method('setTimeZone', DI\get('time_zone'))
    ->method('setDatabase', DI\get('database_name')),
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Doctrine\Schema\Migrator')
  ])
];
