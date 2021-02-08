<?php
return [
  "route.providers" => DI\add([
    DI\get("Starbug\Log\RouteProvider")
  ]),
  "db.schema.migrations" => DI\add([
    DI\get("Starbug\Log\Migration")
  ])
];
