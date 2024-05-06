<?php
namespace Starbug\Core\Script;

use Psr\Container\ContainerInterface;
use Starbug\Core\Generator\Definitions\Host;
use Starbug\Core\Generator\Generator;
use Starbug\Db\Schema\SchemerInterface;

class Setup {
  public function __construct(
    protected Generator $generator,
    protected Host $host,
    protected ContainerInterface $container
  ) {
    $this->generator = $generator;
    $this->host = $host;
    $this->container = $container;
  }
  public function __invoke($named) {
    if (!file_exists("var/etc/di.php")) {
      // Generate host configuration
      $this->generator->generate($this->host, $named);
      $vars = include("var/etc/di.php");
      foreach ($vars as $name => $value) {
        $this->container->set($name, $value);
      }
    }

    // Migrate database
    $this->container->get(SchemerInterface::class)->migrate();
  }
}
