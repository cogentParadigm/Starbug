<?php
use Interop\Container\ContainerInterface;
use Monolog\Logger;

return [
  'environment' => 'development',
  'website_url' => '/',
  'default_path' => 'home',
  'time_zone' => 'America/Vancouver',
  'hmac_key' => '',
  'routes' => [
    "api" => [
      "controller" => "Starbug\\Core\\ApiRoutingController",
      "action" => "response"
    ],
    "profile" => [
      "title" => "Starbug\\Core\\ProfileController",
      "controller" => "profile"
    ],
    "admin" => [
      "title" => "Admin",
      "controller" => "Starbug\\Core\\AdminController",
      "action" => "defaultAction",
      "groups" => "admin",
      "theme" => "storm"
    ],
    "terms" => [
      "template" => "xhr.xhr",
      "groups" => "user"
    ],
    "robots" => [
      "template" => "txt.txt"
    ]
  ],
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
  'Starbug\Core\Routing\RouterInterface' => DI\object('Starbug\Core\Routing\Router')
    ->method('addStorage', DI\get('Starbug\Core\Routing\MemoryRouteStorage')),
  'Starbug\Core\Routing\*Interface' => DI\object('Starbug\Core\Routing\*'),
  'Starbug\Core\Routing\MemoryRouteStorage' => DI\object()->method('addRoutes', DI\get('routes')),
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
