<?php
namespace Starbug\Js;

use DI;
use Starbug\Js\Script\DojoBuild;

return [
  "js.build" => false,
  "template.helpers" => DI\add([
    "dojo" => DojoHelper::class
  ]),
  'Starbug\Js\DojoConfiguration' => DI\autowire()
    ->constructorParameter('isBuild', DI\get('js.build')),
  DojoBuild::class => DI\autowire()
    ->constructorParameter('base_directory', DI\get('base_directory')),
    "scripts.dojo-build" => DojoBuild::class
];
