<?php
namespace Starbug\Users;

use DI;

return [
  "route.providers" => DI\add([
    DI\get("Starbug\Users\RouteProvider")
  ]),
  "db.schema.migrations" => DI\add([
    DI\get("Starbug\Users\Migration")
  ])
];
