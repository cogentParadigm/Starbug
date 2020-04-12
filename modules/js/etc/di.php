<?php
return [
  'Starbug\Js\DojoConfiguration' => DI\autowire()
    ->constructorParameter('environment', DI\get('environment')),
  'Starbug\Js\DojoBuildCommand' => DI\autowire()
    ->constructorParameter('base_directory', DI\get('base_directory'))
];
