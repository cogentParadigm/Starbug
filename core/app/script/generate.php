<?php
namespace Starbug\Core;

use Starbug\Core\Generator\Generator;
use Interop\Container\ContainerInterface;

class GenerateCommand {
  public function __construct(Generator $generator, ContainerInterface $container, $base_directory) {
    $this->generator = $generator;
    $this->base_directory = $base_directory;
    $this->container = $container;
  }
  public function run($argv) {
    $generator = ucwords(array_shift($argv));
    $args = [];
    foreach ($argv as $i => $arg) {
      if (0 === strpos($arg, "-")) {
        $arg = str_replace("-", "", $arg);
        $parts = (false !== strpos($arg, "=")) ? explode("=", $arg, 2) : [$arg, true];
        $args[$parts[0]] = $parts[1];
        $params[$parts[0]] = $parts[1];
      }
    }
    if (empty($args["namespace"])) {
      $args["namespace"] = 'Starbug\Core\Generator\Definitions';
    }
    $definition = $this->container->get($args["namespace"]."\\".$generator);
    $this->generator->generate($definition, $args);
  }
}
