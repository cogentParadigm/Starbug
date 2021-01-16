<?php
namespace Starbug\Core;

use DI\ContainerBuilder;

class ContainerFactory {
  public function __construct($base_directory) {
    $this->base_directory = $base_directory;
  }
  public function build($options = []) {
    $config = include($this->base_directory."/etc/di.php");
    $config["base_directory"] = $this->base_directory;
    $config["modules"] = $config["modules"] ?? [];
    $builder = new ContainerBuilder();
    $builder->addDefinitions($config);
    $this->addDefinitions($builder, $config["modules"]);
    if (!empty($config["t"])) {
      $this->addDefinitions($builder, $config["modules"], "tests/etc");
    }
    $builder->addDefinitions($options);
    $container = $builder->build();
    $container->set('Psr\Container\ContainerInterface', $container);
    return $container;
  }
  protected function addDefinitions(ContainerBuilder $builder, array $modules, string $dir = "etc") {
    foreach ($modules as $module) {
      $path = $module["path"] ."/" . $dir ."/di.php";
      if (file_exists($path)) $builder->addDefinitions($path);
    }
  }
}
