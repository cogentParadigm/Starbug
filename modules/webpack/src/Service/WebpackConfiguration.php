<?php
namespace Starbug\Webpack\Service;

use Starbug\Config\ConfigInterface;

class WebpackConfiguration {
  protected ConfigInterface $config;
  protected string $baseDirectory;
  protected $configuration = false;

  public function __construct(ConfigInterface $config, $baseDirectory = "") {
    $this->config = $config;
    $this->baseDirectory = $baseDirectory;
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
   * Retrieve a specific component of the configuration (options or plugins).
   *
   * @param string $key
   *   Which component - 'options' or 'plugins'.
   *
   * @return array
   *   The configuration data.
   */
  public function get($key) {
    return $this->getConfiguration()[$key];
  }

  /**
   * Generate a Webpack configuration file (webpack.config.js).
   *
   * @return string
   *   The contents for the webpack configuration file.
   */
  public function getWebpackConfig() {
    $output = "const path = require('path');\n\n";
    $config = json_encode($this->get("options"), JSON_PRETTY_PRINT);
    $output .= "let config = ".$config.";\n\n";
    $output .= "config.context = path.resolve(__dirname, '../../');\n";
    $output .= "config.resolve = config.resolve || {};\n";
    $output .= "config.resolve.alias = config.resolve.alias || {};\n";
    $alieses = $this->get("aliases");
    foreach ($alieses as $alias => $path) {
      $output .= "config.resolve.alias['".$alias."'] = path.resolve(config.context, '".$path."');\n";
    }
    $output .= "config.plugins = [];\n";
    $plugins = $this->get("plugins");
    foreach ($plugins as $plugin) {
      $output .= "\nrequire(path.resolve(config.context, '".$plugin."'))(config);";
    }
    $output .= "\nmodule.exports = config;\n";
    return $output;
  }

  /**
   * Internal method to load the configuration data.
   */
  protected function load() {
    if (false === $this->configuration) {
      $this->configuration = $this->config->get("webpack") ?? [];
    }
  }

  public function getAssetPath($file, $defaultDir) {
    $buildDir = "libraries/dist/";
    $buildPath = $buildDir . $file;
    $defaultDir = rtrim($defaultDir, "/")."/";
    if (file_exists($buildPath)) {
      $timestamp = filemtime($buildPath);
      $file .= "?v=" . $timestamp;
      return $buildDir . $file;
    }
    return $defaultDir . $file;
  }
}
