<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/Config.php
 * @author Ali Gangji <ali@neonrain.com>
 */
/**
 * An implementation of the ConfigInterface which reads json files from the filesystem
 */
class Config implements ConfigInterface {

  private $locator;
  private $configs;

  public function __construct(ResourceLocatorInterface $locator) {
    $this->locator = $locator;
    $this->configs = array();
  }

  /**
   * get a configuration value
   * @param string $name the name of the configuration entry, such as 'themes' or 'fixtures.base'
   * @param string $scope the scope/category of the configuration item
   * providing first.second.third will open up the file first.json and look for the key "second" and within that, a key "third"
   */
  public function get($key, $scope="etc") {
    $parts = explode(".", $key);

    $key = array_shift($parts);

    if (empty($this->configs[$key])) {
      $resources = $this->locator->locate($key.".json", $scope);
      $result = array();
      foreach ($resources as $resource) {
        $data = json_decode(file_get_contents($resource));
        $result = array_merge_recursive($result, $data);
      }
      $this->configs[$key] = $result;
    }

    $value = $this->configs[$key];

    while (!empty($parts)) {
      $next = array_shift($parts);
      $value = $value[$next];
    }

    return $value;
  }

}
?>
