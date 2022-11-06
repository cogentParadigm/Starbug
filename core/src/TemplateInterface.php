<?php
namespace Starbug\Core;

/**
 * A simple interface for template rendering.
 */
interface TemplateInterface {

  /**
   * Assign a variable.
   *
   * @param string $key variable name
   * @param string $value variable value
   */
  public function assign($key, $value = null);
  /**
   * Output a rendered template.
   *
   * @param mixed $paths a path or an array of paths to try
   * @param array $params an array of variables to inject
   * @param array $options additional options such as the scope or prefix
   */
  public function render($paths = [""], $params = [], $options = []);

  /**
   * Capture and return a rendered template.
   *
   * @param mixed $paths a path or an array of paths to try
   * @param array $params an array of variables to inject
   * @param array $options additional options such as the scope or prefix
   *
   * @return string the output of the template
   */
  public function capture($paths = [""], $params = [], $options = []);

  /**
   * Render an inline template string and return the result.
   *
   * @param string $template The template string.
   * @param array $params The replacement parameters.
   *
   * @return string The rendered result.
   */
  public function replace($template, $params = []);
}
