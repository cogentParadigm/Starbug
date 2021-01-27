<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Monolog\Logger;
use DI;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\Routing\RoutesHelper;
use Starbug\Http\UriBuilder;
use Starbug\ResourceLocator\ResourceLocator;
use Whoops\Handler\Handler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;

return [
  'environment' => 'development',
  'website_url' => '/',
  'time_zone' => 'UTC',
  'db' => 'default',
  'hmac_key' => '',
  'cli' => false,
  'routes' => [
    "api/{controller}/{action}.{format}" => ["controller" => "Starbug\\Core\\ApiRoutingController", "action" => "response"],
    "profile" => ["controller" => "profile"],
    "robots" => ["template" => "txt.txt"],
    "admin" => RoutesHelper::adminRoute("Starbug\Core\AdminController"),
    "admin/settings" => RoutesHelper::adminRoute("Starbug\Core\AdminSettingsController"),
    "admin/imports/run/{id:[0-9]+}" =>
      RoutesHelper::adminRoute("Starbug\Core\AdminImportsController", ["action" => "run"]),
    "admin/taxonomies/taxonomy/{taxonomy}" =>
      RoutesHelper::adminRoute("Starbug\Core\AdminTaxonomiesController", ["action" => "taxonomy"]),
    "admin/taxonomies/create.xhr" =>
      RoutesHelper::adminRoute("Starbug\Core\AdminTaxonomiesController", ["action" => "create", "format" => "xhr"]),
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
  "Starbug\Auth\*RepositoryInterface" => DI\autowire("Starbug\Auth\Repository\*Repository"),
  "Starbug\Auth\SessionExchangeInterface" => DI\autowire("Starbug\Auth\Http\CookieSessionExchange")
    ->constructorParameter("path", DI\get("website_url"))
    ->constructorParameter("key", DI\get("hmac_key")),
  "Starbug\Auth\Http\CsrfExchangeInterface" => DI\autowire("Starbug\Auth\Http\CookieCsrfExchange")
    ->constructorParameter("path", DI\get("website_url")),
  "Starbug\Auth\SessionHandlerInterface" => DI\autowire("Starbug\Auth\SessionHandler")
    ->method("addHook", DI\get("Starbug\Auth\Http\CsrfHandlerInterface")),
  "Starbug\Auth\*Interface" => DI\autowire("Starbug\Auth\*"),
  "Starbug\Auth\Http\CsrfHandlerInterface" => DI\autowire("Starbug\Auth\Http\CsrfHandler")
  ->constructorParameter("key", DI\get("hmac_key")),
  "Starbug\Http\UriBuilderInterface" => DI\factory(function (ContainerInterface $container, $siteUrl) {
    $request = $container->get("Psr\Http\Message\ServerRequestInterface");
    $baseUri = $request->getUri()
      ->withPath($siteUrl)
      ->withQuery("")
      ->withFragment("");
    return new UriBuilder($baseUri);
  })->parameter("siteUrl", DI\get("website_url")),
  "Psr\Http\Message\UriInterface" => DI\factory(function (ContainerInterface $container, ServerRequestInterface $request) {
    return $request->getUri();
  }),
  "Psr\Http\Message\ResponseFactoryInterface" => DI\autowire("Http\Factory\Guzzle\ResponseFactory"),
  'Starbug\Core\ModelFactoryInterface' => DI\autowire('Starbug\Core\ModelFactory')->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\CssGenerateCommand' => DI\autowire()->constructorParameter('base_directory', DI\get('base_directory')),
  "Whoops\Run" => DI\decorate(function ($whoops, $container) {
    $textHandler = new PlainTextHandler($container->get("Psr\Log\LoggerInterface"));
    $whoops->appendHandler($textHandler);
    $cli = $container->get("cli");
    if (!\Whoops\Util\Misc::isCommandLine() && !$cli) {
      $textHandler->loggerOnly(true);
      if ($container->get("environment") == "production") {
        $whoops->appendHandler(function ($e) use ($container) {
          $response = $container->get("Starbug\Http\ResponseBuilderInterface");
          $response = $response->create(500)
          ->render("exception.html")
          ->getResponse();
          $emitter = new SapiEmitter();
          $emitter->emit($response);
          return Handler::QUIT;
        });
      } else {
        $whoops->appendHandler(new PrettyPageHandler());
      }
    }
    return $whoops;
  }),
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
    ->method('setDatabase', DI\get('db')),
  'Starbug\Core\GenerateCommand' => DI\autowire()->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\SetupCommand' => DI\autowire()->constructorParameter('base_directory', DI\get('base_directory')),
  'Psr\Log\LoggerInterface' => function (ContainerInterface $c) {
    $logger = new Logger("main");
    $env = $c->get("environment");
    $handlers = $c->get("log.handlers.".$env);
    foreach ($handlers as $handler) {
      $logger->pushHandler($handler);
    }
    return $logger;
  },
  "Starbug\ResourceLocator\ResourceLocatorInterface" => function (ContainerInterface $container) {
    $modules = $container->get("Starbug\Modules\Configuration")->getEnabled();
    $locator = new ResourceLocator($container->get("base_directory"));
    $locator->setNamespaces(array_column($modules, "namespace"));
    $locator->setPaths(array_column($modules, "path"));
    return $locator;
  },
  "Middlewares\Https" => DI\autowire()
    ->constructorParameter("responseFactory", DI\get("Http\Factory\Guzzle\ResponseFactory"))
    ->method("includeSubdomains")
];
