<?php

use function DI\add;
use function DI\get;

return [
  "db.schema.migrations" => add([
    get("Starbug\Comments\Migration")
  ])
];
