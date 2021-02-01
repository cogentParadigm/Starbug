<?php
namespace Starbug\Tachyons;

use DI;

return [
  "route.providers" => DI\add([
    DI\get("Starbug\Tachyons\RouteProvider")
  ])
];
