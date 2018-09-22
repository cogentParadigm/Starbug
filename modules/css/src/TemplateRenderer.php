<?php
namespace Starbug\Css;

use Starbug\Core\TemplateInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Twig based template renderer.
 */
class TemplateRenderer implements TemplateInterface {

  public $path = "";
  public $options = [];
  public $defaults = [
    "scope" => "templates",
    "all" => false
  ];
  protected $helpers;
  protected $hooks = [];

  public function __construct(Twig_Environment $twig) {
    $this->twig = $twig;
    $this->twig->addGlobal("this", $this);
  }

  /**
   * Assign variables to be available for templates.
   */
  public function assign($key, $value = "") {
    if (is_array($key)) {
      foreach ($key as $k => $v) $this->assign($k, $v);
    } else {
      $this->twig->addGlobal($key, $value);
    }
  }

  public function output($paths = [], $params = [], $options = []) {
    if (!is_array($paths)) $paths = [$paths];
    $options = $options + $this->defaults;

    // Apply the correct namespace if needed.
    if ($options["scope"] != "templates") {
      $paths = array_map(function ($path) {
        return "@".$options["scope"]."/".$path;
      }, $paths);
    }

    // Render all or render one.
    $results = [];
    if ($options["all"]) {
      $namespaces = $this->twig->getLoader()->getNamespaces();
      foreach ($namespaces as $namespace) {
        if ($namespace !== Twig_Loader_Filesystem::MAIN_NAMESPACE) {
          $result = twig_include($env, $context, "@".$namespace."/".$template, $variables, $withContext, $ignoreMissing, $sandboxed);
          $result = $twig->resolveTemplate("@".$namespace."/".$path)->render($params);
          if ($result) {
            $results[] = $result;
          }
        }
      }
    }
  }

  public function render($paths = [""], $params = [], $options = []) {
    if (!is_array($paths)) $paths = [$paths];
    $options = $options + $this->defaults;

    // Apply the correct namespace if needed.
    if ($options["scope"] != "templates") {
      $paths = array_map(function ($path) use ($options) {
        return "@".$options["scope"]."/".$path;
      }, $paths);
    }

    // Apply extension.
    $paths = array_map(function ($path) {
      return $path.".twig";
    }, $paths);

    $this->twig->resolveTemplate($paths)->display($params);
  }

  public function capture($paths = [""], $params = [], $options = []) {
    if (!is_array($paths)) $paths = [$paths];
    $options = $options + $this->defaults;

    // Apply the correct namespace if needed.
    if ($options["scope"] != "templates") {
      $paths = array_map(function ($path) use ($options) {
        return "@".$options["scope"]."/".$path;
      }, $paths);
    }

    // Apply extension.
    $paths = array_map(function ($path) {
      return $path.".twig";
    }, $paths);

    return $this->twig->resolveTemplate($paths)->render($params);
  }
}
