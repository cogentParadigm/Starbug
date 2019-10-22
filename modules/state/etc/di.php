<?php
return [
  'Starbug\State\StateInterface' => DI\object('Starbug\State\DatabaseState'),
  "db.schema.migrations" => DI\add([
    DI\get("Starbug\State\Migration\State")
  ])
];
