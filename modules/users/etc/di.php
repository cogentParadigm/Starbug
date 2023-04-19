<?php
namespace Starbug\Users;

use function DI\add;
use function DI\get;
use DI;

return [
  "route.providers" => add([
    get("Starbug\Users\RouteProvider")
  ]),
  "db.schema.migrations" => add([
    get("Starbug\Users\Migration")
  ])
];
