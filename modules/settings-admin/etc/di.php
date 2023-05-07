<?php
namespace Starbug\Settings\Admin;

use function DI\add;
use function DI\get;

return [
  "route.providers" => add([
    get(RouteProvider::class)
  ]),
  "db.schema.migrations" => add([
    get(Migration::class)
  ])
];
