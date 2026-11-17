<?php
namespace Starbug\Users\Impersonate;

use Psr\Container\ContainerInterface;

use function DI\add;
use function DI\decorate;
use function DI\get;

return [
  "route.providers" => add([
    get(RouteProvider::class)
  ]),
  "session_handler.hooks" => decorate(function ($hooks, ContainerInterface $container) {
    $hooks[] = $container->get(SessionHandler::class);
    return $hooks;
  }),
  "template.helpers" => add([
    "impersonate" => ImpersonateHelper::class
  ])
];
