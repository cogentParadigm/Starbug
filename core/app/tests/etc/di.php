<?php
return [
  'db' => 'test',
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Core\TestsMigration')
  ])
];
