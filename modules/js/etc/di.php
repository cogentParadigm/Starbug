<?php
namespace Starbug\Js;

use DI;

return [
  "template.helpers" => DI\add([
    "dojo" => DojoHelper::class
  ]),
  'Starbug\Js\DojoConfiguration' => DI\autowire()
    ->constructorParameter('environment', DI\get('environment')),
  'Starbug\Js\DojoBuildCommand' => DI\autowire()
    ->constructorParameter('base_directory', DI\get('base_directory'))
];
