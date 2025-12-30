<?php
namespace Starbug\Dojo;

use function DI\add;
use function DI\autowire;
use function DI\get;
use Starbug\Dojo\Helper\DojoHelper;
use Starbug\Dojo\Script\DojoBuild;

return [
  "js.build" => false,
  "template.helpers" => add([
    "dojo" => DojoHelper::class
  ]),
  'Starbug\Dojo\Service\DojoConfiguration' => autowire()
    ->constructorParameter('isBuild', get('js.build')),
  DojoBuild::class => autowire()
    ->constructorParameter('base_directory', get('base_directory')),
    "scripts.dojo-profile" => DojoBuild::class
];
