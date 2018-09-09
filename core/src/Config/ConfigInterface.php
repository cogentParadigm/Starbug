<?php
namespace Starbug\Core;
/**
 * a simple interface for retrieving configuration data
 */
interface ConfigInterface {
  /**
   * get a configuration value
   * @param string $name the name of the configuration entry, such as 'themes' or 'fixtures.base'
   * @param string $scope the scope/category of the configuration item
   */
  public function get($key, $scope = "etc");
}
