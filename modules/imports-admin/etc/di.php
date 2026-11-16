<?php
namespace Starbug\Imports\Admin;

use function DI\add;
use function DI\get;

return [
  "route.providers" => add([
    get(RouteProvider::class)
  ])
];
