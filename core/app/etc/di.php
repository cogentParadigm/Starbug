<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Monolog\Logger;
use DI;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use GuzzleHttp\Psr7\ServerRequest;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\Script\Generate;
use Starbug\Core\Script\ListScripts;
use Starbug\Core\Script\ProcessQueue;
use Starbug\Core\Script\Queue;
use Starbug\Core\Script\Setup;
use Starbug\Http\UriBuilder;
use Starbug\Queue\QueueFactory;
use Starbug\ResourceLocator\ResourceLocator;
use Whoops\Handler\Handler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;

return [
  "environment" => "development",
  "website_url" => "/",
  "website_host" => "",
  "time_zone" => "UTC",
  "db" => "default",
  "hmac_key" => "",
  "cli" => false,
  "FastRoute\RouteCollector" => DI\decorate(function (RouteCollector $r, ContainerInterface $c) {
    $routes = $c->get("Starbug\Core\Routing\Configuration")->getRoutes();
    foreach ($routes as $route) {
      $r->addRoute("GET", $route->getPath(), $route);
    }
    return $r;
  }),
  "route.providers" => [
    DI\get("Starbug\Core\Admin\RouteProvider"),
    DI\get("Starbug\Core\Admin\Menus\RouteProvider"),
    DI\get("Starbug\Core\Admin\Imports\RouteProvider"),
    DI\get("Starbug\Core\Api\RouteProvider")
  ],
  "Starbug\Core\Routing\Configuration" => DI\autowire()->method("addProviders", DI\get("route.providers")),
  'Starbug\Core\SettingsInterface' => DI\autowire('Starbug\Core\DatabaseSettings'),
  'Starbug\Core\*Interface' => DI\autowire('Starbug\Core\*'),
  'Starbug\Config\*Interface' => DI\autowire('Starbug\Config\*'),
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
  "Psr\Http\Message\ServerRequestInterface" => function (ContainerInterface $container) {
    return new ServerRequest("GET", $container->get("website_host").$container->get("website_url"));
  },
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
    ->method('setDatabase', DI\get('Starbug\Core\DatabaseInterface')),
  'Starbug\Core\ImportsFieldsForm' => DI\autowire()
    ->method('setFilesystems', DI\get('League\Flysystem\MountManager'))
    ->method("setModels", DI\get("Starbug\Core\ModelFactoryInterface"))
    ->method("setSchema", DI\get("Starbug\Db\Schema\SchemerInterface"))
    ->method("setDatabase", DI\get("Starbug\Core\DatabaseInterface")),
  'db.schema.migrations' => [
    DI\get('Starbug\Core\Migration')
  ],
  'db.schema.hooks' => [
    DI\get('Starbug\Core\SchemaHook')
  ],
  'Starbug\Core\Database' => DI\autowire()
    ->method('setTimeZone', DI\get('time_zone'))
    ->method('setDatabase', DI\get("databases.active")),
  "template.helpers" => [
    "breadcrumbs" => BreadcrumbsHelper::class,
    "collections" => CollectionsHelper::class,
    "config" => ConfigHelper::class,
    "csrf" => CsrfHelper::class,
    "db" => DbHelper::class,
    "displays" => DisplaysHelper::class,
    "filesystems" => FilesystemsHelper::class,
    "filter" => FilterHelper::class,
    "images" => ImagesHelper::class,
    "request" => RequestHelper::class,
    "session" => SessionHelper::class,
    "settings" => SettingsHelper::class,
    "url" => UrlHelper::class
  ],
  "form.hooks" => [
    "category_select" => FormCategorySelectHook::class,
    "checkbox" => FormCheckboxHook::class,
    "file" => FormFileHook::class,
    "hidden" => FormHiddenHook::class,
    "html" => FormHtmlHook::class,
    "input" => FormInputHook::class,
    "multiple_category_select" => FormMultipleCategorySelectHook::class,
    "multiple_select" => FormMultipleSelectHook::class,
    "password" => FormPasswordHook::class,
    "radio" => FormRadioHook::class,
    "radio_select" => FormRadioSelectHook::class,
    "select" => FormSelectHook::class,
    "submit" => FormSubmitHook::class,
    "tag_select" => FormTagSelectHook::class,
    "template" => FormTemplateHook::class,
    "textarea" => FormTextareaHook::class,
    "text" => FormTextHook::class
  ],
  "macro.hooks" => [
    "site" => MacroSiteHook::class,
    "url" => MacroUrlHook::class
  ],
  "scripts.generate" => Generate::class,
  "scripts.process-queue" => ProcessQueue::class,
  "scripts.queue" => Queue::class,
  "scripts.setup" => Setup::class,
  "scripts.list-scripts" => ListScripts::class,
  'Starbug\Core\Script\Generate' => DI\autowire()->constructorParameter('base_directory', DI\get('base_directory')),
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
    ->method("includeSubdomains"),
  "Starbug\Bundle\*Interface" => DI\autowire("Starbug\Bundle\*"),
  "Starbug\Operation\*Interface" => DI\autowire("Starbug\Operation\*"),
  "Starbug\Core\SettingsForm" => DI\autowire()->method("setDatabase", DI\get("Starbug\Core\DatabaseInterface")),
  "Starbug\Queue\*Interface" => DI\autowire("Starbug\Queue\*"),
  "Starbug\Queue\QueueFactoryInterface" => function (ContainerInterface $container) {
    $factory = new QueueFactory();
    $factory->addQueue("default", function () use ($container) {
      return $container->make("Starbug\Queue\Driver\Sql", ["name" => "default"]);
    });
    return $factory;
  },
  FormHookFactoryInterface::class => DI\autowire(FormHookFactory::class)
    ->constructorParameter("hooks", DI\get("form.hooks")),
  MacroHookFactoryInterface::class => DI\autowire(MacroHookFactory::class)
    ->constructorParameter("hooks", DI\get("macro.hooks"))
];
