<?php
namespace Starbug\Core;

use function DI\get;
use function DI\autowire;
use function DI\factory;
use function DI\add;
use Psr\Container\ContainerInterface;
use Starbug\Core\Script\Generate;
use Starbug\Core\Script\ListScripts;
use Starbug\Core\Script\Setup;
use Starbug\ResourceLocator\ResourceLocator;
use Starbug\Http\Helper\RequestHelper;
use Starbug\Http\Helper\UrlHelper;
use Starbug\Auth\Helper\CsrfHelper;
use Starbug\Auth\Helper\SessionHelper;

return [
  "environment" => "development",
  "website_url" => "/",
  "website_host" => "",
  "time_zone" => "UTC",
  "hmac_key" => "",
  "cli" => false,
  'Starbug\Core\*Interface' => autowire('Starbug\Core\*'),
  'Starbug\Config\*Interface' => autowire('Starbug\Config\*'),
  "template.helpers" => [
    "collections" => CollectionsHelper::class,
    "config" => ConfigHelper::class,
    "csrf" => CsrfHelper::class,
    "db" => DbHelper::class,
    "images" => ImagesHelper::class,
    "request" => RequestHelper::class,
    "session" => SessionHelper::class,
    "url" => UrlHelper::class
  ],
  "macro.hooks" => add([
    "site" => MacroSiteHook::class,
    "url" => MacroUrlHook::class
  ]),
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
  "Starbug\Bundle\*Interface" => autowire("Starbug\Bundle\*"),
  "Starbug\Operation\*Interface" => autowire("Starbug\Operation\*"),
  "Starbug\Core\SettingsForm" => autowire()->method("setDatabase", get("Starbug\Db\DatabaseInterface")),
];
