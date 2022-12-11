<?php
namespace Starbug\Intl;

use DI;
use Starbug\Intl\Script\IntlSetup;

return [
  "route.providers" => DI\add([
    DI\get("Starbug\Intl\RouteProvider")
  ]),
  "db.schema.migrations" => DI\add([
    DI\get("Starbug\Intl\Migration")
  ]),
  "Starbug\Intl\*Interface" => DI\autowire("Starbug\Intl\*"),
  "template.helpers" => DI\add([
    "addressFormatter" => AddressFormatterHelper::class
  ]),
  "form.hooks" => DI\add([
    "address" => FormAddressHook::class
  ]),
  "scripts.intl-setup" => IntlSetup::class
];
