<?php
namespace Starbug\Core;

use DI\ContainerBuilder;

class ContainerFactory {
  protected $defaults = [
    "db" => "default",
    "cli" => false
  ];
  public function __construct($base_directory) {
    $this->base_directory = $base_directory;
  }
  public function build($options = []) {
    $options = is_array($options) ? $options + $this->defaults : $this->defaults;
    $di = include($this->base_directory."/etc/di.php");
    $di["base_directory"] = $this->base_directory;
    $di["database_name"] = $options["db"];
    $di["cli"] = $options["cli"];
    $locator = new ResourceLocator($di['base_directory'], $di['modules']);
    $builder = new ContainerBuilder();
    $builder->addDefinitions($di);
    foreach ($locator->locate("di.php", "etc") as $defs) $builder->addDefinitions($defs);
    if (!empty($options["t"])) {
      foreach ($locator->locate("di.php", "tests/etc") as $defs) $builder->addDefinitions($defs);
    }
    $container = $builder->build();
    $container->set('Interop\Container\ContainerInterface', $container);
    $container->set('Starbug\Core\ResourceLocatorInterface', $locator);
    return $container;
  }
}
