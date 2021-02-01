<?php
return [
  "route.providers" => DI\add([
    DI\get("Starbug\App\Page\RouteProvider")
  ]),
  "db.schema.migrations" => DI\add([
    DI\get("Starbug\App\Migration")
  ])
];
