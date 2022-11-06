<?php
namespace Starbug\Css;

use Starbug\Core\TemplateInterface;
use Twig\Environment;

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

  public function __construct(Environment $twig) {
    $this->twig = $twig;
    $this->twig->addGlobal("this", $this);
  }

  /**
   * Assign variables to be available for templates.
   */
  public function assign($key, $value = "") {
    if (is_array($key)) {
      foreach ($key as $k => $v) {
        $this->assign($k, $v);
      }
    } else {
      $this->twig->addGlobal($key, $value);
    }
  }

  public function render($paths = [""], $params = [], $options = []) {
    if (!is_array($paths)) {
      $paths = [$paths];
    }
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
    if (!is_array($paths)) {
      $paths = [$paths];
    }
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

  public function replace($template, $params = []) {
    return $this->twig->createTemplate((string) $template)->render($params);
  }
}
