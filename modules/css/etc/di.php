<?php
namespace Starbug\Css;

use function DI\add;
use function DI\autowire;
use function DI\get;
use function DI\decorate;
use DI;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extension\StringLoaderExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

return [
  'theme' => 'tachyons',
  "template.helpers" => add([
    "css" => CssHelper::class
  ]),
  'Starbug\Css\CssLoader' => autowire()
    ->constructorParameter('theme', get('theme'))
    ->constructorParameter('baseUrl', get('website_url')),
  'Starbug\Css\RouteFilter' => autowire()->constructorParameter('theme', get('theme')),
  'Starbug\Core\Routing\RouterInterface' => decorate(function ($router, ContainerInterface $container) {
    $router->addFilter($container->get('Starbug\Css\RouteFilter'));
    return $router;
  }),
  'Twig\Environment' => function (ContainerInterface $c) {
    // Configure loader.
    $loader = new FilesystemLoader();
    $modules = array_reverse($c->get("Starbug\Modules\Configuration")->getEnabled(), true);
    foreach ($modules as $name => $module) {
      $dir = $module["path"];
      if (file_exists($dir."/templates")) {
        $loader->addPath($dir."/templates");
        $loader->addPath($dir."/templates", $name);
      }
      if (file_exists($dir."/layouts")) {
        $loader->addPath($dir."/layouts", "layouts");
      }
      if (file_exists($dir."/views")) {
        $loader->addPath($dir."/views", "views");
      }
    }
    // Initialize environment.
    $twig = new Environment($loader, ['debug' => true, 'html_errors' => true, 'autoescape' => function ($template) {
      $parts = explode(",", $template);
      array_pop($parts);
      $ext = array_pop($parts);
      if (in_array($ext, ["html", "css", "js"])) {
        return $ext;
      }
      return false;
    }]);
    // Add helper function.
    $helpers = $c->get("template.helpers");
    $helperFunction = new TwigFunction('helper', function ($name) use ($helpers, $c) {
      return $c->get($helpers[$name])->helper();
    });
    $twig->addFunction($helperFunction);
    // Add publish function.
    $publish = new TwigFunction('publish', function (Environment $env, $context, $template, $variables = [], $withContext = true, $ignoreMissing = true, $sandboxed = false) use ($c) {
      $results = [];
      $modules = array_reverse($c->get("Starbug\Modules\Configuration")->getEnabled(), true);
      foreach ($modules as $namespace => $module) {
        if ($namespace !== FilesystemLoader::MAIN_NAMESPACE) {
          $result = twig_include($env, $context, "@".$namespace."/".$template, $variables, $withContext, $ignoreMissing, $sandboxed);
          if ($result) {
            array_unshift($results, $result);
          }
        }
      }
      return implode("\n", $results);
    }, ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['all']]);
    $twig->addFunction($publish);
    // Add preg_replace.
    $twig->addFilter(new TwigFilter('preg_replace', function ($subject, $pattern, $replacement) {
      return preg_replace($pattern, $replacement, $subject);
    }));
    // Add CSV output
    $twig->addFunction(new TwigFunction('csv', function ($data) {
      $out = fopen('php://output', 'w');
      foreach ($data as $row) {
        fputcsv($out, $row);
      }
      fclose($out);
    }));
    // Add extensions.
    $twig->addExtension(new StringLoaderExtension());
    $twig->addExtension(new DebugExtension());
    return $twig;
  },
  'Starbug\Core\TemplateInterface' => autowire('Starbug\Css\TemplateRenderer')
];
