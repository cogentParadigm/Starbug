<?php
return [
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Log\Migration')
  ])
];
