<?php
namespace Starbug\Intl;

use function DI\add;
use function DI\get;
use function DI\autowire;
use DI;
use Starbug\Intl\Script\IntlSetup;

return [
  "route.providers" => add([
    get("Starbug\Intl\RouteProvider")
  ]),
  "db.schema.migrations" => add([
    get("Starbug\Intl\Migration")
  ]),
  "Starbug\Intl\*Interface" => autowire("Starbug\Intl\*"),
  "template.helpers" => add([
    "addressFormatter" => AddressFormatterHelper::class
  ]),
  "form.hooks" => add([
    "address" => FormAddressHook::class
  ]),
  "scripts.intl-setup" => IntlSetup::class
];
