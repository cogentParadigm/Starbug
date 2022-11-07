<?php
namespace Starbug\Content;

use DI;
use Psr\Container\ContainerInterface;

return [
  "route.providers" => DI\add([
    DI\get("Starbug\Content\RouteProvider")
  ]),
  'db.schema.migrations' => DI\add([
    DI\get('Starbug\Content\Migration')
  ]),
  'Starbug\Core\Routing\RouterInterface' => DI\decorate(function ($router, ContainerInterface $container) {
    $router->addAliasStorage($container->get('Starbug\Content\AliasStorage'));
    return $router;
  }),
  "db.query.executor.hooks" => DI\add([
    "path" => "Starbug\Content\StorePathHook",
    "blocks" => "Starbug\Content\StoreBlocksHook"
  ]),
  "form.hooks" => DI\add([
    "blocks" => FormBlocksHook::class
  ])
];
