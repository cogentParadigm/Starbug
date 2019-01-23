<?php
namespace Starbug\Core;

/**
 * A simple interface for retrieving configuration data
 */
interface ConfigInterface {
  /**
   * Get a configuration value
   *
   * @param string $name the name of the configuration entry, such as 'themes' or 'fixtures.base'
   * @param string $scope the scope/category of the configuration item
   */
  public function get($key, $scope = "etc");
  /**
   * Set a configuration value
   *
   * @param string $name the name of the configuration entry, such as 'themes' or 'fixtures.base'
   * @param string $value The value to set
   * @param string $scope the scope/category of the configuration item
   */
  public function set($key, $value);
}
