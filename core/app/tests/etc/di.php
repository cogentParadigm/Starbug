<?php

use function DI\add;
use function DI\get;

return [
  'db' => 'test',
  'db.schema.migrations' => add([
    get('Starbug\Core\TestsMigration')
  ])
];
