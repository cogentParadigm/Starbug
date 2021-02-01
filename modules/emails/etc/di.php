<?php
namespace Starbug\Emails;

use DI;

return [
  "route.providers" => DI\add([
    DI\get("Starbug\Emails\RouteProvider")
  ]),
  "db.schema.migrations" => DI\add([
    DI\get("Starbug\Emails\Migration")
  ])
];
