<?php
namespace Starbug\Core;

/**
 * Template class. assign/render style templating system
 */
class Template implements TemplateInterface {

  public $locator;
  private $vars = [];
  public $path = "";
  public $options = [];
  public $defaults = array(
    "scope" => "templates",
    "all" => false
  );
  protected $helpers;

  function __construct(ResourceLocatorInterface $locator, HelperFactoryInterface $helpers = null, $options = []) {
    $this->options = $options + $this->defaults;
    $this->locator = $locator;
    $this->helpers = $helpers;
  }

  /**
   * @copydoc TemplateInterface::assign
   */
  function assign($key, $value = "") {
    if (is_array($key)) {
      foreach ($key as $k => $v) $this->assign($k, $v);
    } else {
      $this->vars[$key] = $value;
    }
  }
  /**
   * @copydoc TemplateInterface::output
   */
  function output($paths = [], $params = [], $options = []) {
    $this->options = $options + $this->options;
    $this->vars = $params + $this->vars;
    $scope = $this->options['scope'];
    if (!is_array($paths)) $paths = array($paths);
    $path = reset($paths);
    $found = [];
    while (empty($found) && $path) {
      $found = $this->locator->locate($path.".php", $scope);
      $path = next($paths);
    }
    $this->path = ($this->options['all']) ? $found : end($found);

    if (!is_array($this->path) && !file_exists($this->path)) {
      throw new \Exception("template not found: ".(is_array($paths) ? implode("\n", $paths) : $paths));
    }

    extract($this->vars);
    if (is_array($this->path)) {
      foreach ($this->path as $p) include($p);
    } else {
      include($this->path);
    }
  }

  /**
   * @copydoc TemplateInterface::get
   */
  function get($paths = [], $params = [], $options = []) {
    ob_start();
    $this->output($paths, $params, $options);
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
  }

  /**
   * @copydoc TemplateInterface::render
   */
  function render($paths = array(""), $params = [], $options = []) {
    $template = new static($this->locator, $this->helpers);
    $template->output($paths, $params + $this->vars, $options + array("scope" => "templates", "all" => false) + $this->options);
  }

  /**
   * @copydoc TemplateInterface::capture
   */
  function capture($paths = array(""), $params = [], $options = []) {
    $template = new static($this->locator, $this->helpers);
    return $template->get($paths, $params + $this->vars, $options + array("scope" => "templates", "all" => false) + $this->options);
  }

  /**
   * @copydoc TemplateInterface::render_view
   */
  function render_view($paths = array(""), $params = []) {
    $this->render($paths, $params, array("scope" => "views"));
  }

  /**
   * @copydoc TemplateInterface::render_layout
   */
  function render_layout($paths = array(""), $params = []) {
    $this->render($paths, $params, array("scope" => "layouts"));
  }

  /**
   * @copydoc TemplateInterface::render_content
   */
  function render_content($region = "content") {
    $this->render("blocks", array("region" => $region));
  }

  /**
   * @copydoc TemplateInterface::publish
   */
  function publish($topic, $tags = [], $params = []) {
    if (!is_array($tags)) $tags = array($tags);
    array_unshift($tags, "global");
    foreach ($tags as $tag) {
      $this->render("hook/".$tag.".".$topic, $params, array("all" => true));
    }
  }

  public function __get($name) {
    return $this->helpers->get($name)->helper();
  }
}
