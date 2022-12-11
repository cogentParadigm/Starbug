<?php
namespace Starbug\Js;

use DI;
use Starbug\Js\Script\DojoBuild;

return [
  "template.helpers" => DI\add([
    "dojo" => DojoHelper::class
  ]),
  'Starbug\Js\DojoConfiguration' => DI\autowire()
    ->constructorParameter('environment', DI\get('environment')),
  DojoBuild::class => DI\autowire()
    ->constructorParameter('base_directory', DI\get('base_directory')),
    "scripts.dojo-build" => DojoBuild::class
];
