<?php
namespace Starbug\Dojo\Service;

use Starbug\Config\ConfigInterface;

class DojoConfiguration {
  protected $configuration = false;
  protected $dependencies = [];
  public function __construct(ConfigInterface $config, $isBuild = false) {
    $this->config = $config;
    $this->isBuild = $isBuild;
  }
  /**
   * Add a dependency.
   *
   * @param string $module
   *   Module identifier.
   */
  public function addDependency($module) {
    $this->dependencies[$module] = true;
  }

  /**
   * Remove a dependency.
   *
   * @param string $module
   *   Module identifier.
   */
  public function removeDependency($module) {
    unset($this->dependencies[$module]);
  }
  /**
   * Retrieve the complete configuration.
   *
   * @return array
   *   The configuration data.
   */
  public function getConfiguration() {
    $this->load();
    return $this->configuration;
  }
  /**
   * Retrieve a specific component of the configuration (layers or prefixes).
   *
   * @param string $key
   *   Which component - 'layers' or 'prefixes'.
   *
   * @return array
   *   The configuration data.
   */
  public function get($key) {
    $this->load();
    return $this->configuration[$key];
  }
  /**
   * Generate a build profile based on the current configuration.
   *
   * @return string
   *   The file contents for the build profile.
   */
  public function getBuildProfile() {
    $this->load();
    $packages = json_encode($this->get("config")["packages"] ?? [], JSON_PRETTY_PRINT);
    $map = json_encode($this->get("config")["map"] ?? [], JSON_PRETTY_PRINT);
    $packages = str_replace(["\n    ", "\n}", "\n]"], ["\n      ", "\n    }", "\n    ]"], $packages);
    $map = str_replace(["\n    ", "\n}", "\n]"], ["\n      ", "\n    }", "\n    ]"], $map);
    return <<<JS
module.exports = function(env) {
  return {
    "baseUrl": env.baseUrl,
    "async": true,
    "map": {$map},
    "packages": {$packages}
  };
};
JS;
  }
  /**
   * Retrieve dependencies which should be required on load.
   *
   * @param string $name
   *   The layer name for which you want the dependencies.
   *
   * @return string
   *   A javascript array of dependencies for the Dojo loader.
   */
  public function getDependencies() {
    $dependencies = $this->get("require") ?? [];
    $dependencies = array_merge($dependencies, array_keys($this->dependencies));
    return '["'.implode('", "', $dependencies).'"]';
  }
  /**
   * Retrieve the loader configuration.
   *
   * @return string
   *   A javascript configuration object for the Dojo loader.
   */
  public function getDojoConfig() {
    $config = $this->get("config");
    if ($this->isBuild) {
      unset($config["packages"]);
    }
    return json_encode($config);
  }
  /**
   * Get the build mode.
   *
   * @return bool
   *   true if using build.
   */
  public function isBuild() {
    return $this->isBuild;
  }
  /**
   * Internal method to load the configuration data.
   */
  protected function load() {
    if (false === $this->configuration) {
      $this->configuration = $this->config->get("dojo");
    }
  }
}
