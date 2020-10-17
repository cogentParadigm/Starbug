<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Monolog\Logger;
use DI;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use Starbug\Core\Routing\RoutesHelper;

return [
  'environment' => 'development',
  'website_url' => '/',
  'default_path' => 'home',
  'time_zone' => 'UTC',
  'hmac_key' => '',
  'routes' => [
    "api/{controller}/{action}" => ["controller" => "Starbug\\Core\\ApiRoutingController", "action" => "response"],
    "profile" => ["controller" => "profile"],
    "robots" => ["template" => "txt.txt"],
    "admin" => RoutesHelper::adminRoute("Starbug\Core\AdminController"),
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
  'Starbug\Core\SettingsInterface' => DI\autowire('Starbug\Core\DatabaseSettings'),
  'Starbug\Core\*Interface' => DI\autowire('Starbug\Core\*'),
  'Starbug\Http\*Interface' => DI\autowire('Starbug\Http\*'),
  'Starbug\Core\ModelFactoryInterface' => DI\autowire('Starbug\Core\ModelFactory')->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\CssGenerateCommand' => DI\autowire()->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\ErrorHandler' => DI\decorate(function ($previous, $container) {
    $cli = $container->get("cli");
    if (false === $cli) {
      $previous->setTemplate("exception.html");
      $previous->setContentOnly(false);
    }
    return $previous;
  }),
  'Starbug\Core\SessionStorageInterface' => DI\autowire('Starbug\Core\SessionStorage')
    ->constructorParameter('key', DI\get('hmac_key')),
  'Starbug\Http\UrlInterface' => function (ContainerInterface $c) {
    $request = $c->get("Starbug\Http\RequestInterface");
    return $request->getUrl();
  },
  "FastRoute\RouteParser" => DI\autowire("FastRoute\RouteParser\Std"),
  "FastRoute\DataGenerator" => DI\autowire("FastRoute\DataGenerator\GroupCountBased"),
  "FastRoute\Dispatcher" => function (ContainerInterface $c) {
    $collector = $c->get("FastRoute\RouteCollector");
    return new GroupCountBased($collector->getData());
  },
  'Starbug\Core\Routing\RouterInterface' => DI\autowire('Starbug\Core\Routing\Router')
    ->method('addStorage', DI\get('Starbug\Core\Routing\FastRouteStorage')),
  'Starbug\Core\Routing\*Interface' => DI\autowire('Starbug\Core\Routing\*'),
  'Starbug\Core\ImagesInterface' => DI\autowire('Starbug\Core\Images')
    ->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\ImportsForm' => DI\autowire()
    ->method('setFilesystems', DI\get('League\Flysystem\MountManager'))
    ->method('setModels', DI\get('Starbug\Core\ModelFactoryInterface')),
  'Starbug\Core\ImportsFieldsForm' => DI\autowire()->method('setFilesystems', DI\get('League\Flysystem\MountManager')),
  'db.schema.migrations' => [
    DI\get('Starbug\Core\Migration')
  ],
  'db.schema.hooks' => [
    DI\get('Starbug\Core\SchemaHook')
  ],
  'Starbug\Core\Database' => DI\autowire()
    ->method('setTimeZone', DI\get('time_zone'))
    ->method('setDatabase', DI\get('database_name')),
  'Starbug\Core\GenerateCommand' => DI\autowire()->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\ApplicationInterface' => DI\autowire('Starbug\Core\Application')
    ->method('setLogger', DI\get('Psr\Log\LoggerInterface')),
  'Starbug\Core\SetupCommand' => DI\autowire()->constructorParameter('base_directory', DI\get('base_directory')),
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
