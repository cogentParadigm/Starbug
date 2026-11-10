<?php
namespace Starbug\Users;

use function DI\autowire;
use function DI\add;
use function DI\get;
use DI;
use Starbug\Users\Query\Hook\StoreOwnerHook;
use Starbug\Users\Query\Hook\StorePasswordHook;

return [
  "route.providers" => add([
    get("Starbug\Users\RouteProvider")
  ]),
  "db.schema.migrations" => add([
    get("Starbug\Users\Migration")
  ]),
  "passwordStrength.passingScore" => 3,
  "passwordStrength.performStrengthTests" => true,
  StorePasswordHook::class => autowire()->constructorParameter('passingScore', get('passwordStrength.passingScore'))
    ->constructorParameter('performStrengthTests', get('passwordStrength.performStrengthTests')),
  "db.query.executor.hooks" => add([
    "owner" => StoreOwnerHook::class,
    "password" => StorePasswordHook::class
  ])
];
