<?php
return [
  'routes' => DI\add([
    "address" => [
      "controller" => "address"
    ]
  ]),
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Intl\Migration')
  ])
];
