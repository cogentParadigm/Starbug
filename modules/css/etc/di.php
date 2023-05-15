<?php
namespace Starbug\Css;

use function DI\add;
use function DI\autowire;
use function DI\get;
use function DI\decorate;
use Psr\Container\ContainerInterface;
use Starbug\Css\Helper\CssHelper;
use Starbug\Routing\RouterInterface;

return [
  'theme' => 'tachyons',
  "template.helpers" => add([
    "css" => CssHelper::class
  ]),
  'Starbug\Css\CssLoader' => autowire()
    ->constructorParameter('theme', get('theme'))
    ->constructorParameter('baseUrl', get('website_url')),
  'Starbug\Css\RouteFilter' => autowire()->constructorParameter('theme', get('theme')),
  RouterInterface::class => decorate(function ($router, ContainerInterface $container) {
    $router->addFilter($container->get('Starbug\Css\RouteFilter'));
    return $router;
  })
];
