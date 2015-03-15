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
class Template {

  private $vars = array();
  public $path = "";
  public $options = array();
  public $defaults = array(
    "scope" => "templates",
    "prefix" => false,
    "all" => false
  );

  function __construct($paths=array(), $vars=array(), $options=array()) {
    $this->options = $options + $this->defaults;
    $this->vars = $vars;
    if (empty($paths)) return;

    //resolve path
    $prefix = $this->options['prefix'];
    $scope = $this->options['scope'];
    if (!is_array($paths)) $paths = array($paths);
    $path = reset($paths);
    $found = array();
    while(empty($found) && $path) {
      if (!empty($prefix)) {
        $prefix_path = BASE_DIR."/".$prefix.$scope."/".$path.".php";
        if (file_exists($prefix_path)) $found[] = $prefix_path;
      } else $found = locate($path.".php", $scope);
      $path = next($paths);
    }
    $this->path = ($this->options['all']) ? $found : end($found);
    if (!is_array($this->path) && !file_exists($this->path)) {
      throw new Exception("template not found: ".(is_array($paths) ? implode("\n", $paths) : $paths));
    }
  }

  /**
   * assign a variable
   * @param string $key variable name
   * @param string $value variable value
   */
  function assign($key, $value="") {
    if (is_array($key)) {
      foreach ($key as $k => $v) $this->assign($k, $v);
    } else {
      $this->vars[$key] = $value;
    }
  }
  /**
   * render a template
   * @param string $path relative path to the template from the view directory without file extension
   */
  function output($params=array()) {
    extract($params + $this->vars);
    if (is_array($this->path)) {
      foreach ($this->path as $p) include($p);
    } else {
      include($this->path);
    }
  }

  /**
   * capture a rendered template
   * @param string $path relative path to the template from the view directory without file extension
   */
  function get($params=array()) {
    ob_start();
    $this->output($params);
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
  }

  /**
   * render a child template
   * @param mixed $paths a path or an array of paths to try
   * @param array $params an array of variables to inject
   * @param array $options additional options such as the scope or prefix
   */
  function render($paths=array(""), $params=array(), $options=array()) {
    $template = new Template($paths, $params + $this->vars, $options + array("scope" => "templates", "all" => false) + $this->options);
    $template->output();
  }

  /**
   * capture a child template
   * @param mixed $paths a path or an array of paths to try
   * @param array $params an array of variables to inject
   * @param array $options additional options such as the scope or prefix
   * @return string the output of the template
   */
  function capture($paths=array(""), $params=array(), $options=array()) {
    $template = new Template($paths, $params + $this->vars, $options + array("scope" => "templates", "all" => false) + $this->options);
    return $template->get();
  }

  /**
   * convenience method to render a template from the views directory
   * @copydoc render
   */
  function render_view($paths=array(""), $params=array()) {
    $this->render($paths, $params, array("scope" => "views"));
  }

  /**
   * convenience method to render a template from the layouts directory
   * @copydoc render
   */
  function render_layout($paths=array(""), $params=array()) {
    $this->render($paths, $params, array("scope" => "layouts"));
  }

  /**
   * convenience method to render a template from the forms directory
   * @copydoc render
   */
  function render_form($paths=array(""), $params=array()) {
    $this->render($paths, $params, array("scope" => "forms"));
  }

  /**
   * render content blocks from the database for the specified region
   * @param string $region the region to render content for
   */
  function render_content($region="content") {
    $this->render("blocks", array("region" => $region));
  }

  /**
   *
   */
  function publish($topic, $tags=array(), $params=array()) {
    if (!is_array($tags)) $tags = array($tags);
    array_unshift($tags, "global");
    foreach ($tags as $tag) {
      $this->render("hook/".$tag.".".$topic, $params, array("all" => true));
    }
  }

  /**
   * build a display
   * @param string $type the display type (list, table, grid, csv, etc..)
   * @param array $model the model to get results from
   * @param string $name the display/query name (admin, list, select, etc..).
   * 										 For example, if you specify 'admin', then the the following model functions will be used:
   * 										 query provider: query_admin
   * 										 display provider: display_admin
   * @param array $options parameters that will be passed to the display and query functions
   */
  function build_display($type, $model=null, $name=null, $options=array()) {
     $class = get_module_class("displays/".ucwords($type)."Display", "lib/Display", "core");
     $display = new $class($this, $model, $name, $options);
    return $display;
  }
  /**
   * build and render a display
   * @param string $type the display type (list, table, grid, csv, etc..)
   * @param array $model the model to get results from
   * @param string $name the display/query name (admin, list, select, etc..).
   * 										 For example, if you specify 'admin', then the the following model functions will be used:
   * 										 query provider: query_admin
   * 										 display provider: display_admin
   * @param array $options parameters that will be passed to the display and query functions
   */
  function render_display($type, $model=null, $name=null, $options=array()) {
    $display = $this->build_display($type, $model, $name, $options);
    $display->render();
  }
  /**
   * build and capture a display
   * @param string $type the display type (list, table, grid, csv, etc..)
   * @param array $model the model to get results from
   * @param string $name the display/query name (admin, list, select, etc..).
   * 										 For example, if you specify 'admin', then the the following model functions will be used:
   * 										 query provider: query_admin
   * 										 display provider: display_admin
   * @param array $options parameters that will be passed to the display and query functions
   */
  function capture_display($type, $model=null, $name=null, $options=array()) {
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
