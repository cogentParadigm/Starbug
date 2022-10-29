<?php
namespace Starbug\Intl;

use DI;

return [
  "route.providers" => DI\add([
    DI\get("Starbug\Intl\RouteProvider")
  ]),
  "db.schema.migrations" => DI\add([
    DI\get("Starbug\Intl\Migration")
  ]),
  "Starbug\Intl\*Interface" => DI\autowire("Starbug\Intl\*")
];
