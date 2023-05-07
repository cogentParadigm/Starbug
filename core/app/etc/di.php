<?php
namespace Starbug\Core;

use function DI\decorate;
use function DI\get;
use function DI\autowire;
use function DI\factory;
use Psr\Container\ContainerInterface;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;
use Starbug\Core\Script\Generate;
use Starbug\Core\Script\ListScripts;
use Starbug\Core\Script\Setup;
use Starbug\Http\UriBuilder;
use Starbug\ResourceLocator\ResourceLocator;

return [
  "environment" => "development",
  "website_url" => "/",
  "website_host" => "",
  "time_zone" => "UTC",
  "hmac_key" => "",
  "cli" => false,
  "FastRoute\RouteCollector" => decorate(function (RouteCollector $r, ContainerInterface $c) {
    $routes = $c->get("Starbug\Core\Routing\Configuration")->getRoutes();
    foreach ($routes as $route) {
      $r->addRoute("GET", $route->getPath(), $route);
    }
    return $r;
  }),
  "route.providers" => [
    get("Starbug\Core\Admin\RouteProvider"),
    get("Starbug\Core\Api\RouteProvider")
  ],
  "Starbug\Core\Routing\Configuration" => autowire()->method("addProviders", get("route.providers")),
  'Starbug\Core\*Interface' => autowire('Starbug\Core\*'),
  'Starbug\Config\*Interface' => autowire('Starbug\Config\*'),
  'Starbug\Http\*Interface' => autowire('Starbug\Http\*'),
  "Starbug\Auth\*RepositoryInterface" => autowire("Starbug\Auth\Repository\*Repository"),
  "Starbug\Auth\SessionExchangeInterface" => autowire("Starbug\Auth\Http\CookieSessionExchange")
    ->constructorParameter("path", get("website_url"))
    ->constructorParameter("key", get("hmac_key")),
  "Starbug\Auth\Http\CsrfExchangeInterface" => autowire("Starbug\Auth\Http\CookieCsrfExchange")
    ->constructorParameter("path", get("website_url")),
  "Starbug\Auth\SessionHandlerInterface" => autowire("Starbug\Auth\SessionHandler")
    ->method("addHook", get("Starbug\Auth\Http\CsrfHandlerInterface")),
  "Starbug\Auth\*Interface" => autowire("Starbug\Auth\*"),
  "Starbug\Auth\Http\CsrfHandlerInterface" => autowire("Starbug\Auth\Http\CsrfHandler")
  ->constructorParameter("key", get("hmac_key")),
  "Starbug\Http\UriBuilderInterface" => factory(function (ContainerInterface $container, $siteUrl) {
    $request = $container->get("Psr\Http\Message\ServerRequestInterface");
    $baseUri = $request->getUri()
      ->withPath($siteUrl)
      ->withQuery("")
      ->withFragment("");
    return new UriBuilder($baseUri);
  })->parameter("siteUrl", get("website_url")),
  "Psr\Http\Message\UriInterface" => factory(function (ContainerInterface $container, ServerRequestInterface $request) {
    return $request->getUri();
  }),
  "Psr\Http\Message\ResponseFactoryInterface" => autowire("Http\Factory\Guzzle\ResponseFactory"),
  "Psr\Http\Message\ServerRequestInterface" => function (ContainerInterface $container) {
    return new ServerRequest("GET", $container->get("website_host").$container->get("website_url"));
  },
  "FastRoute\RouteParser" => autowire("FastRoute\RouteParser\Std"),
  "FastRoute\DataGenerator" => autowire("FastRoute\DataGenerator\GroupCountBased"),
  "FastRoute\Dispatcher" => function (ContainerInterface $c) {
    $collector = $c->get("FastRoute\RouteCollector");
    return new GroupCountBased($collector->getData());
  },
  'Starbug\Core\Routing\RouterInterface' => autowire('Starbug\Core\Routing\Router')
    ->method('addStorage', get('Starbug\Core\Routing\FastRouteStorage')),
  'Starbug\Core\Routing\*Interface' => autowire('Starbug\Core\Routing\*'),
  'Starbug\Core\ImagesInterface' => autowire('Starbug\Core\Images')
    ->constructorParameter('base_directory', get('base_directory')),
  'db.schema.migrations' => [
    get('Starbug\Core\Migration')
  ],
  'db.schema.hooks' => [
    get('Starbug\Core\SchemaHook')
  ],
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
  "scripts.setup" => Setup::class,
  "scripts.list-scripts" => ListScripts::class,
  'Starbug\Core\Script\Generate' => autowire()->constructorParameter('base_directory', get('base_directory')),
  "Starbug\ResourceLocator\ResourceLocatorInterface" => function (ContainerInterface $container) {
    $modules = $container->get("Starbug\Modules\Configuration")->getEnabled();
    $locator = new ResourceLocator($container->get("base_directory"));
    $locator->setNamespaces(array_column($modules, "namespace"));
    $locator->setPaths(array_column($modules, "path"));
    return $locator;
  },
  "Middlewares\Https" => autowire()
    ->constructorParameter("responseFactory", get("Http\Factory\Guzzle\ResponseFactory"))
    ->method("includeSubdomains"),
  "Starbug\Bundle\*Interface" => autowire("Starbug\Bundle\*"),
  "Starbug\Operation\*Interface" => autowire("Starbug\Operation\*"),
  "Starbug\Core\SettingsForm" => autowire()->method("setDatabase", get("Starbug\Db\DatabaseInterface")),
  FormHookFactoryInterface::class => autowire(FormHookFactory::class)
    ->constructorParameter("hooks", get("form.hooks")),
  MacroHookFactoryInterface::class => autowire(MacroHookFactory::class)
    ->constructorParameter("hooks", get("macro.hooks"))
];
