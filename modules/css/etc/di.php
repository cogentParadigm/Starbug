<?php
use Interop\Container\ContainerInterface;

return [
  'theme' => 'tachyons',
  'Starbug\Css\CssLoader' => DI\object()
    ->constructorParameter('modules', DI\get('modules')),
  'Starbug\Css\RouteFilter' => DI\object()->constructorParameter('theme', DI\get('theme')),
  'Starbug\Css\CssBuildCommand' => DI\object()->constructorParameter('base_directory', DI\get('base_directory')),
  'Starbug\Core\Routing\RouterInterface' => DI\object()
    ->method('addFilter', DI\get('Starbug\Css\RouteFilter')),
  'Twig_Environment' => function (ContainerInterface $c) {
    // Configure loader.
    $loader = new Twig_Loader_Filesystem();
    $modules = array_reverse($c->get("modules"));
    foreach ($modules as $ns => $dir) {
      $ns = strtolower(explode("\\", $ns)[1]);
      if (file_exists($dir."/templates")) {
        $loader->addPath($dir."/templates");
        $loader->addPath($dir."/templates", $ns);
      }
      if (file_exists($dir."/layouts")) {
        $loader->addPath($dir."/layouts", "layouts");
      }
      if (file_exists($dir."/views")) {
        $loader->addPath($dir."/views", "views");
      }
    }
    // Initialize environment.
    $twig = new Twig_Environment($loader, ['debug' => true, 'html_errors' => true, 'autoescape' => function ($template) {
      $parts = explode(",", $template);
      array_pop($parts);
      $ext = array_pop($parts);
      if (in_array($ext, ["html", "css", "js"])) return $ext;
      return false;
    }]);
    // Add helper function.
    $helpers = $c->get("Starbug\Core\HelperFactoryInterface");
    $helperFunction = new Twig_Function('helper', function ($name) use ($helpers) {
      return $helpers->get($name)->helper();
    });
    $twig->addFunction($helperFunction);
    // Add publish function.
    $publish = new Twig_Function('publish', function (Twig_Environment $env, $context, $template, $variables = [], $withContext = true, $ignoreMissing = true, $sandboxed = false) {
      $results = [];
      $namespaces = $env->getLoader()->getNamespaces();
      foreach ($namespaces as $namespace) {
        if ($namespace !== Twig_Loader_Filesystem::MAIN_NAMESPACE) {
          $result = twig_include($env, $context, "@".$namespace."/".$template, $variables, $withContext, $ignoreMissing, $sandboxed);
          if ($result) {
            $results[] = $result;
          }
        }
      }
      return implode("\n", $results);
    }, ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['all']]);
    $twig->addFunction($publish);
    // Add preg_replace.
    $twig->addFilter(new Twig_Filter('preg_replace', function ($subject, $pattern, $replacement) {
      return preg_replace($pattern, $replacement, $subject);
    }));
    // Add CSV output
    $twig->addFunction(new Twig_Function('csv', function ($data) {
      $out = fopen('php://output', 'w');
      foreach ($data as $row) {
        fputcsv($out, $row);
      }
      fclose($out);
    }));
    // Add extensions.
    $twig->addExtension(new Twig_Extension_StringLoader());
    $twig->addExtension(new Twig_Extension_Debug());
    return $twig;
  },
  'Starbug\Core\TemplateInterface' => DI\object('Starbug\Css\TemplateRenderer')
];
