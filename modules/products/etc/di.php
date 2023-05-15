<?php
namespace Starbug\Products;

use function DI\add;
use function DI\get;

return [
  "db.schema.migrations" => add([
    get(Migration::class)
  ])
];
