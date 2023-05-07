<?php
namespace Starbug\Settings;

use Starbug\Settings\Helper\SettingsHelper;

use function DI\add;
use function DI\autowire;
use function DI\get;

return [
  "db.schema.migrations" => add([
    get(Migration::class)
  ]),
  "template.helpers" => [
    "settings" => SettingsHelper::class
  ],
  SettingsInterface::class => autowire(DatabaseSettings::class)
];
