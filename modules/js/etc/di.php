<?php
namespace Starbug\Js;

use function DI\add;
use function DI\autowire;
use function DI\get;
use Starbug\Js\Helper\DojoHelper;
use Starbug\Js\Script\DojoBuild;

return [
  "js.build" => false,
  "template.helpers" => add([
    "dojo" => DojoHelper::class
  ]),
  'Starbug\Js\DojoConfiguration' => autowire()
    ->constructorParameter('isBuild', get('js.build')),
  DojoBuild::class => autowire()
    ->constructorParameter('base_directory', get('base_directory')),
    "scripts.dojo-build" => DojoBuild::class
];
