<?php
namespace Starbug\Users;

use function DI\autowire;
use function DI\add;
use function DI\get;
use DI;

return [
  "route.providers" => add([
    get("Starbug\Users\RouteProvider")
  ]),
  "db.schema.migrations" => add([
    get("Starbug\Users\Migration")
  ]),
  "passwordStrength.passingScore" => 3,
  "passwordStrength.performStrengthTests" => true,
  "Starbug\Db\Query\Hook\StorePasswordHook" => autowire()->constructorParameter('passingScore', get('passwordStrength.passingScore'))
    ->constructorParameter('performStrengthTests', get('passwordStrength.performStrengthTests'))
];
