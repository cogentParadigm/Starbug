<?php
use Interop\Container\ContainerInterface;
use Monolog\Logger;
use League\Flysystem\MountManager;
use Starbug\Core\Storage\Filesystem;
use Starbug\Core\Storage\Adapter\Local;
use Starbug\Core\URL;

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
      "action" => "default_action",
      "groups" => "admin",
      "theme" => "storm"
    ],
    "upload" => [
      "title" => "Starbug\\Core\\UploadController",
      "controller" => "upload",
      "template" => "xhr.xhr",
      "groups" => "user"
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
  'Starbug\Core\ResourceLocator' => DI\object()->constructor(DI\get('base_directory'), DI\get('modules')),
  'Starbug\Core\ModelFactory' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\CssGenerateCommand' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\ErrorHandler' => DI\decorate(function ($previous, $container) {
    $cli = $container->get("cli");
    if (false === $cli) {
      $previous->setTemplate("exception.html");
    }
    return $previous;
  }),
  'Starbug\Core\SessionStorage' => DI\object()->constructorParameter('key', DI\get('hmac_key')),
  'Starbug\Core\URLInterface' => function (ContainerInterface $c) {
    $request = $c->get("Starbug\Core\RequestInterface");
    return $request->getURL();
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
  'Starbug\Core\Template' => DI\object()->constructorParameter('helpers', DI\get('Starbug\Core\HelperFactoryInterface')),
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
  },
  'filesystem.adapters' => ['default', 'public', 'tmp'],
  'filesystem.adapter.default' => 'public',
  'filesystem.public' => 'var/public/uploads',
  'filesystem.tmp' => 'var/tmp',
  'filesystem.adapter.public' => function (ContainerInterface $c) {
    $here = $c->get("Starbug\Core\URLInterface");
    $public = $c->get("filesystem.public");
    $url = (new URL($here->getHost(), $here->getDirectory().$public."/"))->setScheme($here->getScheme());
    $adapter = new Local($c->get("base_directory")."/".$public);
    $adapter->setURLInterface($url);
    return $adapter;
  },
  'filesystem.adapter.tmp' => function (ContainerInterface $c) {
    $here = $c->get("Starbug\Core\URLInterface");
    $tmp = $c->get("filesystem.tmp");
    $url = (new URL($here->getHost(), $here->getDirectory().$tmp."/"))->setScheme($here->getScheme());
    $adapter = new Local($c->get("base_directory")."/".$tmp);
    $adapter->setURLInterface($url);
    return $adapter;
  },
  'League\Flysystem\MountManager' => function (ContainerInterface $c) {
    $manager = new MountManager();
    $adapters = $c->get("filesystem.adapters");
    foreach ($adapters as $prefix) {
      $adapter = $c->get('filesystem.adapter.'.$prefix);
      if (is_string($adapter)) $adapter = $c->get("filesystem.adapter.".$adapter);
      $manager->mountFilesystem($prefix, new Filesystem($adapter));
    }
    return $manager;
  },
  'Starbug\Core\Storage\FilesystemInterface' => function (ContainerInterface $c) {
    $manager = $c->get("League\Flysystem\MountManager");
    return $manager->getFilesystem("default");
  }
];
