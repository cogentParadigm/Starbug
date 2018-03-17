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

  function __construct(Twig_Environment $twig) {
    $this->twig = $twig;
    $this->twig->addGlobal("this", $this);
  }

  /**
   * @copydoc TemplateInterface::assign
   */
  public function assign($key, $value = "") {
    if (is_array($key)) {
      foreach ($key as $k => $v) $this->assign($k, $v);
    } else {
      $this->twig->addGlobal($key, $value);
    }
  }

  /**
   * @copydoc TemplateInterface::output
   */
  function output($paths = array(), $params = array(), $options = array()) {
    if (!is_array($paths)) $paths = array($paths);
    $options = $options + $this->defaults;

    // Apply the correct namespace if needed.
    if ($options["scope"] != "templates") {
      $paths = array_map(function($path) {
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

  /**
   * @copydoc TemplateInterface::render
   */
  function render($paths = array(""), $params = array(), $options = array()) {
    if (!is_array($paths)) $paths = array($paths);
    $options = $options + $this->defaults;

    // Apply the correct namespace if needed.
    if ($options["scope"] != "templates") {
      $paths = array_map(function($path) use ($options) {
        return "@".$options["scope"]."/".$path;
      }, $paths);
    }

    // Apply extension.
    $paths = array_map(function($path) {
      return $path.".twig";
    }, $paths);

    $this->twig->resolveTemplate($paths)->display($params);
  }

  /**
   * @copydoc TemplateInterface::capture
   */
  function capture($paths = array(""), $params = array(), $options = array()) {
    if (!is_array($paths)) $paths = array($paths);
    $options = $options + $this->defaults;

    // Apply the correct namespace if needed.
    if ($options["scope"] != "templates") {
      $paths = array_map(function($path) use ($options) {
        return "@".$options["scope"]."/".$path;
      }, $paths);
    }

    // Apply extension.
    $paths = array_map(function($path) {
      return $path.".twig";
    }, $paths);

    return $this->twig->resolveTemplate($paths)->render($params);
  }

}
