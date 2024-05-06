<?php
namespace Starbug\Core\Script;

use Starbug\Core\Generator\Generator;
use Psr\Container\ContainerInterface;

class Generate {
  public function __construct(
    protected Generator $generator,
    protected ContainerInterface $container,
    protected $base_directory
  ) {
  }
  public function __invoke($positional, $named) {
    $generator = ucwords(array_shift($positional));
    if (false == strpos($generator, "\\")) {
      $generator = "Starbug\Core\Generator\Definitions"."\\".$generator;
    }
    $definition = $this->container->get($generator);
    $this->generator->generate($definition, $named);
  }
}
