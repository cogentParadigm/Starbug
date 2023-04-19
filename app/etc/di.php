<?php

use function DI\add;
use function DI\get;

return [
  "route.providers" => add([
    get("Starbug\App\Page\RouteProvider")
  ]),
  "db.schema.migrations" => add([
    get("Starbug\App\Migration")
  ])
];
