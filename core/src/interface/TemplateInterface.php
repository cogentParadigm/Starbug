<?php
namespace Starbug\Core;

/**
 * A simple interface for template rendering.
 */
interface TemplateInterface {

  /**
  * assign a variable
  * @param string $key variable name
  * @param string $value variable value
  */
  public function assign($key, $value = null);
  /**
  * render a child template
  * @param mixed $paths a path or an array of paths to try
  * @param array $params an array of variables to inject
  * @param array $options additional options such as the scope or prefix
  */
  public function render($paths = array(""), $params = array(), $options = array());

  /**
  * capture a child template
  * @param mixed $paths a path or an array of paths to try
  * @param array $params an array of variables to inject
  * @param array $options additional options such as the scope or prefix
  * @return string the output of the template
  */
  public function capture($paths = array(""), $params = array(), $options = array());
}
