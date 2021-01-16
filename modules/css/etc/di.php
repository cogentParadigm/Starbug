<?php
namespace Starbug\Css;

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
  'Starbug\Css\CssLoader' => DI\autowire()
    ->constructorParameter('theme', DI\get('theme')),
  'Starbug\Css\RouteFilter' => DI\autowire()->constructorParameter('theme', DI\get('theme')),
  'Starbug\Css\CssBuildCommand' => DI\autowire()->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\Routing\RouterInterface' => DI\decorate(function ($router, ContainerInterface $container) {
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
      if (in_array($ext, ["html", "css", "js"])) return $ext;
      return false;
    }]);
    // Add helper function.
    $helpers = $c->get("Starbug\Core\HelperFactoryInterface");
    $helperFunction = new TwigFunction('helper', function ($name) use ($helpers) {
      return $helpers->get($name)->helper();
    });
    $twig->addFunction($helperFunction);
    // Add publish function.
    $publish = new TwigFunction('publish', function (Environment $env, $context, $template, $variables = [], $withContext = true, $ignoreMissing = true, $sandboxed = false) {
      $results = [];
      $namespaces = $env->getLoader()->getNamespaces();
      foreach ($namespaces as $namespace) {
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
  'Starbug\Core\TemplateInterface' => DI\autowire('Starbug\Css\TemplateRenderer')
];
