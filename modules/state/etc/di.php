<?php

use function DI\autowire;
use function DI\add;
use function DI\get;

return [
  'Starbug\State\StateInterface' => autowire('Starbug\State\DatabaseState'),
  "db.schema.migrations" => add([
    get("Starbug\State\Migration\State")
  ])
];
