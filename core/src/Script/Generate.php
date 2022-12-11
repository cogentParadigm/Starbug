<?php
namespace Starbug\Core\Script;

use Starbug\Core\Generator\Generator;
use Psr\Container\ContainerInterface;

class Generate {
  public function __construct(Generator $generator, ContainerInterface $container, $base_directory) {
    $this->generator = $generator;
    $this->base_directory = $base_directory;
    $this->container = $container;
  }
  public function __invoke($positional, $named) {
    $generator = ucwords(array_shift($positional));
    if (empty($named["namespace"])) {
      $named["namespace"] = 'Starbug\Core\Generator\Definitions';
    }
    $definition = $this->container->get($named["namespace"]."\\".$generator);
    $this->generator->generate($definition, $named);
  }
}
