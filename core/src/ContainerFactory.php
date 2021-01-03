<?php
namespace Starbug\Core;

use DI\ContainerBuilder;
use Starbug\ResourceLocator\ResourceLocator;

class ContainerFactory {
  public function __construct($base_directory) {
    $this->base_directory = $base_directory;
  }
  public function build($options = []) {
    $config = include($this->base_directory."/etc/di.php");
    $config["base_directory"] = $this->base_directory;
    $config["modules"] = $config["modules"] ?? [];
    $locator = new ResourceLocator($config['base_directory'], $config['modules']);
    $builder = new ContainerBuilder();
    $builder->addDefinitions($config);
    foreach ($locator->locate("di.php", "etc") as $defs) $builder->addDefinitions($defs);
    if (!empty($config["t"])) {
      foreach ($locator->locate("di.php", "tests/etc") as $defs) $builder->addDefinitions($defs);
    }
    $builder->addDefinitions($options);
    $container = $builder->build();
    $container->set('Psr\Container\ContainerInterface', $container);
    $container->set('Starbug\ResourceLocator\ResourceLocatorInterface', $locator);
    return $container;
  }
}
