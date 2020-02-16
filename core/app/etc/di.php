<?php
namespace Starbug\Core;

use Interop\Container\ContainerInterface;
use Monolog\Logger;
use DI;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use Starbug\Core\Routing\RoutesHelper;

return [
  'environment' => 'development',
  'website_url' => '/',
  'default_path' => 'home',
  'time_zone' => 'America/Vancouver',
  'hmac_key' => '',
  'routes' => [
    "api/{controller}/{action}" => ["controller" => "Starbug\\Core\\ApiRoutingController", "action" => "response"],
    "profile" => ["controller" => "profile"],
    "robots" => ["template" => "txt.txt"],
    "admin/settings" => RoutesHelper::adminRoute("Starbug\Core\AdminSettingsController"),
    "admin/imports/run/{id:[0-9]+}" =>
      RoutesHelper::adminRoute("Starbug\Core\AdminImportsController", ["action" => "run"]),
    "admin/taxonomies/taxonomy/{taxonomy}" =>
      RoutesHelper::adminRoute("Starbug\Core\AdminTaxonomiesController", ["action" => "taxonomy"]),
    "admin/menus/menu/{menu}" =>
      RoutesHelper::adminRoute("Starbug\Core\AdminMenusController", ["action" => "menu"])
  ]
  + RoutesHelper::crudRoutes("admin/taxonomies", "Starbug\Core\AdminTaxonomiesController")
  + RoutesHelper::crudiRoutes("admin/menus", "Starbug\Core\AdminMenusController")
  + RoutesHelper::crudRoutes("admin/imports", "Starbug\Core\AdminImportsController")
  + RoutesHelper::crudRoutes("admin/imports_fields", "Starbug\Core\AdminImportsFieldsController"),
  "FastRoute\RouteCollector" => DI\decorate(function (RouteCollector $r, ContainerInterface $c) {
    $routes = $c->get("routes");
    foreach ($routes as $pattern => $route) {
      $r->addRoute("GET", "/" . $pattern, $route);
    }
    return $r;
  }),
  'Starbug\Core\SettingsInterface' => DI\object('Starbug\Core\DatabaseSettings'),
  'Starbug\Core\*Interface' => DI\object('Starbug\Core\*'),
  'Starbug\Http\*Interface' => DI\object('Starbug\Http\*'),
  'Starbug\Core\ResourceLocator' => DI\object()->constructor(DI\get('base_directory'), DI\get('modules')),
  'Starbug\Core\ModelFactory' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\CssGenerateCommand' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\ErrorHandler' => DI\decorate(function ($previous, $container) {
    $cli = $container->get("cli");
    if (false === $cli) {
      $previous->setTemplate("exception.html");
      $previous->setContentOnly(false);
    }
    return $previous;
  }),
  'Starbug\Core\SessionStorage' => DI\object()->constructorParameter('key', DI\get('hmac_key')),
  'Starbug\Http\UrlInterface' => function (ContainerInterface $c) {
    $request = $c->get("Starbug\Http\RequestInterface");
    return $request->getUrl();
  },
  "FastRoute\RouteParser" => DI\object("FastRoute\RouteParser\Std"),
  "FastRoute\DataGenerator" => DI\object("FastRoute\DataGenerator\GroupCountBased"),
  "FastRoute\Dispatcher" => function (ContainerInterface $c) {
    $collector = $c->get("FastRoute\RouteCollector");
    return new GroupCountBased($collector->getData());
  },
  'Starbug\Core\Routing\RouterInterface' => DI\object('Starbug\Core\Routing\Router')
    ->method('addStorage', DI\get('Starbug\Core\Routing\FastRouteStorage')),
  'Starbug\Core\Routing\*Interface' => DI\object('Starbug\Core\Routing\*'),
  'Starbug\Core\Images' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\ImportsForm' => DI\object()->method('setFilesystems', DI\get('League\Flysystem\MountManager')),
  'Starbug\Core\ImportsFieldsForm' => DI\object()->method('setFilesystems', DI\get('League\Flysystem\MountManager')),
  'db.schema.migrations' => [
    DI\get('Starbug\Core\Migration')
  ],
  'db.schema.hooks' => [
    DI\get('Starbug\Core\SchemaHook')
  ],
  'Starbug\Core\Database' => DI\object()
    ->method('setTimeZone', DI\get('time_zone'))
    ->method('setDatabase', DI\get('database_name')),
  'Starbug\Core\GenerateCommand' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\Application' => DI\object()->method('setLogger', DI\get('Psr\Log\LoggerInterface')),
  'Starbug\Core\SetupCommand' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
  'Psr\Log\LoggerInterface' => function (ContainerInterface $c) {
    $logger = new Logger("main");
    $env = $c->get("environment");
    $handlers = $c->get("log.handlers.".$env);
    foreach ($handlers as $handler) {
      $logger->pushHandler($handler);
    }
    return $logger;
  }
];
