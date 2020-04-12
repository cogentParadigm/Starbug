<?php
return [
  'Starbug\State\StateInterface' => DI\autowire('Starbug\State\DatabaseState'),
  "db.schema.migrations" => DI\add([
    DI\get("Starbug\State\Migration\State")
  ])
];
