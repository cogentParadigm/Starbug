<?php
namespace Starbug\Admin;

use function DI\add;
use function DI\get;
use Starbug\Admin\Api\RouteProvider as ApiRouteProvider;

return [
  "route.providers" => add([
    get(RouteProvider::class),
    get(ApiRouteProvider::class)
  ])
];
