<?php
namespace Starbug\Content;

use function DI\add;
use function DI\autowire;
use function DI\get;
use function DI\decorate;
use Psr\Container\ContainerInterface;
use Starbug\Content\Form\FormBlocksHook;
use Starbug\Content\MenuCollection as ContentMenuCollection;
use Starbug\Content\Query\StoreBlocksHook;
use Starbug\Content\Query\StorePathHook;
use Starbug\Menus\Collection\MenuCollection;
use Starbug\Routing\RouterInterface;

return [
  "route.providers" => add([
    get("Starbug\Content\RouteProvider")
  ]),
  'db.schema.migrations' => add([
    get('Starbug\Content\Migration')
  ]),
  RouterInterface::class => decorate(function ($router, ContainerInterface $container) {
    $router->addAliasStorage($container->get('Starbug\Content\AliasStorage'));
    return $router;
  }),
  "db.query.executor.hooks" => add([
    "path" => StorePathHook::class,
    "blocks" => StoreBlocksHook::class
  ]),
  "form.hooks" => add([
    "blocks" => FormBlocksHook::class
  ]),
  MenuCollection::class => autowire(ContentMenuCollection::class)
];
