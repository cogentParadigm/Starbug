<?php
namespace Starbug\Js;

use Starbug\Config\ConfigInterface;

class DojoConfiguration {
  protected $configuration = false;
  protected $dependencies = [];
  public function __construct(
    protected ConfigInterface $config,
    protected $isBuild = false
  ) {
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
    $profile = [
      "layers" => $this->get("layers"),
      "prefixes" => []
    ];
    $dependencies = $this->get("require") ?? [];
    foreach ($profile["layers"] as &$layer) {
      if ($layer["name"] == "dojo.js") {
        $layer["dependencies"] = array_merge($dependencies, $layer["dependencies"]);
      }
    }
    $packages = $this->get("config")["packages"];
    foreach ($packages as $package) {
      $profile["prefixes"][] = [$package["name"], $package["location"]];
    }
    return "dependencies = ".json_encode($profile);
  }
  /**
   * Retrieve dependencies which should be required on load.
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
