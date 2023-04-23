<?php
namespace Starbug\Content;

use function DI\add;
use function DI\autowire;
use function DI\get;
use function DI\decorate;
use Psr\Container\ContainerInterface;
use Starbug\Content\MenuCollection as ContentMenuCollection;
use Starbug\Menus\Collection\MenuCollection;

return [
  "route.providers" => add([
    get("Starbug\Content\RouteProvider")
  ]),
  'db.schema.migrations' => add([
    get('Starbug\Content\Migration')
  ]),
  'Starbug\Core\Routing\RouterInterface' => decorate(function ($router, ContainerInterface $container) {
    $router->addAliasStorage($container->get('Starbug\Content\AliasStorage'));
    return $router;
  }),
  "db.query.executor.hooks" => add([
    "path" => "Starbug\Content\StorePathHook",
    "blocks" => "Starbug\Content\StoreBlocksHook"
  ]),
  "form.hooks" => add([
    "blocks" => FormBlocksHook::class
  ]),
  MenuCollection::class => autowire(ContentMenuCollection::class)
];
