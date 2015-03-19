<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/Renderer.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup Renderer
 */
/**
 * @defgroup Renderer
 * The starbug templating engine, see @link core/global/templates.php for global functions
 * @ingroup lib
 */
/**
 * Renderer class. assign/render style templating engine
 * @ingroup Renderer
 */
class Template implements TemplateInterface {

  private $locator;
  private $vars = array();
  public $path = "";
  public $options = array();
  public $defaults = array(
    "scope" => "templates",
    "all" => false
  );

  function __construct(ResourceLocatorInterface $locator, $options=array()) {
    $this->options = $options + $this->defaults;
    $this->locator = $locator;
  }

  /**
   * @copydoc TemplateInterface::assign
   */
  function assign($key, $value="") {
    if (is_array($key)) {
      foreach ($key as $k => $v) $this->assign($k, $v);
    } else {
      $this->vars[$key] = $value;
    }
  }
  /**
   * @copydoc TemplateInterface::output
   */
  function output($paths=array(), $params=array(), $options=array()) {
    $this->options = $options + $this->options;
    $this->vars = $params + $this->vars;
    $prefix = $this->options['prefix'];
    $scope = $this->options['scope'];
    if (!is_array($paths)) $paths = array($paths);
    $path = reset($paths);
    $found = array();
    while(empty($found) && $path) {
      $found = $this->locator->locate($path.".php", $scope);
      $path = next($paths);
    }
    $this->path = ($this->options['all']) ? $found : end($found);

    if (!is_array($this->path) && !file_exists($this->path)) {
      exit();
      throw new Exception("template not found: ".(is_array($paths) ? implode("\n", $paths) : $paths));
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
  function get($paths=array(), $params=array(), $options=array()) {
    ob_start();
    $this->output($paths, $params, $options);
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
  }

  /**
   * @copydoc TemplateInterface::render
   */
  function render($paths=array(""), $params=array(), $options=array()) {
    $template = new Template($this->locator);
    $template->output($paths, $params + $this->vars, $options + array("scope" => "templates", "all" => false) + $this->options);
  }

  /**
   * @copydoc TemplateInterface::capture
   */
  function capture($paths=array(""), $params=array(), $options=array()) {
    $template = new Template($this->locator);
    return $template->get($paths, $params + $this->vars, $options + array("scope" => "templates", "all" => false) + $this->options);
  }

  /**
   * @copydoc TemplateInterface::render_view
   */
  function render_view($paths=array(""), $params=array()) {
    $this->render($paths, $params, array("scope" => "views"));
  }

  /**
   * @copydoc TemplateInterface::render_layout
   */
  function render_layout($paths=array(""), $params=array()) {
    $this->render($paths, $params, array("scope" => "layouts"));
  }

  /**
   * @copydoc TemplateInterface::render_form
   */
  function render_form($paths=array(""), $params=array()) {
    $this->render($paths, $params, array("scope" => "forms"));
  }

  /**
   * @copydoc TemplateInterface::render_content
   */
  function render_content($region="content") {
    $this->render("blocks", array("region" => $region));
  }

  /**
   * @copydoc TemplateInterface::publish
   */
  function publish($topic, $tags=array(), $params=array()) {
    if (!is_array($tags)) $tags = array($tags);
    array_unshift($tags, "global");
    foreach ($tags as $tag) {
      $this->render("hook/".$tag.".".$topic, $params, array("all" => true));
    }
  }

  /**
   * @copydoc TemplateInterface::build_display
   */
  function build_display($type, $model=null, $name=null, $options=array()) {
     $class = get_module_class("displays/".ucwords($type)."Display", "lib/Display", "core");
     $display = new $class($this, $model, $name, $options);
    return $display;
  }
  /**
   * @copydoc TemplateInterface::render_display
   */
  public function render_display($type, $model=null, $name=null, $options=array()) {
    $display = $this->build_display($type, $model, $name, $options);
    $display->render();
  }
  /**
   * @copydoc TemplateInterface::capture_display
   */
  public function capture_display($type, $model=null, $name=null, $options=array()) {
    $display = $this->build_display($type, $model, $name, $options);
    return $display->capture();
  }
  /**
   * render a hook
   * @param string $name the name of the hook
   * @param array $params parameters to add to the template vars
   * @param array $options options to add to the template options
   */
  function render_hook($name, $params=array(), $options=array()) {
    $hook = build_hook("template/".$name, "lib/TemplateHook", "core");
    $hook->render($this, $params + $this->vars, $options + $this->options);
  }
}
?>
