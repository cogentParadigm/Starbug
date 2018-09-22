<?php
namespace Starbug\Core;

class DisplaysHelper {
  public function __construct(DisplayFactoryInterface $displays) {
    $this->displays = $displays;
  }
  public function helper() {
    return $this;
  }
  /**
   * Build a display.
   *
   * @param string $type the display type (list, table, grid, csv, etc..)
   * @param array $model the model to get results from
   * @param string $name the display/query name (admin, list, select, etc..).
   *                      For example, if you specify 'admin', then the the following model functions will be used:
   *                      query provider: query_admin
   *                      display provider: display_admin
   * @param array $options parameters that will be passed to the display and query functions
   */
  public function build($name, $options = []) {
    $display = $this->displays->get($name);
    $display->build($options);
    return $display;
  }
  /**
   * Build and render a display.
   *
   * @param string $type the display type (list, table, grid, csv, etc..)
   * @param array $model the model to get results from
   * @param string $name the display/query name (admin, list, select, etc..).
   *                      For example, if you specify 'admin', then the the following model functions will be used:
   *                      query provider: query_admin
   *                      display provider: display_admin
   * @param array $options parameters that will be passed to the display and query functions
   */
  public function render($name, $options = []) {
    $display = $this->build($name, $options);
    $display->render();
  }
  /**
   * Build and capture a display.
   *
   * @param string $type the display type (list, table, grid, csv, etc..)
   * @param array $model the model to get results from
   * @param string $name the display/query name (admin, list, select, etc..).
   *                      For example, if you specify 'admin', then the the following model functions will be used:
   *                      query provider: query_admin
   *                      display provider: display_admin
   * @param array $options parameters that will be passed to the display and query functions
   */
  public function capture($name, $options = []) {
    $display = $this->build($name, $options);
    return $display->capture();
  }
}
