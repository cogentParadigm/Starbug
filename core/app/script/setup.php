<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Starbug\Core\Generator\Definitions\Host;
use Starbug\Core\Generator\Generator;
use Starbug\Db\Schema\SchemerInterface;

class SetupCommand {
  public function __construct(Generator $generator, Host $host, ContainerInterface $container) {
    $this->generator = $generator;
    $this->host = $host;
    $this->container = $container;
  }
  public function run($argv) {
    $positional = [];
    $named = [];
    foreach ($argv as $i => $arg) {
      if (0 === strpos($arg, "-")) {
        $arg = ltrim($arg, "-");
        $parts = (false !== strpos($arg, "=")) ? explode("=", $arg, 2) : [$arg, true];
        $named[$parts[0]] = $parts[1];
      } else {
        $positional[] = $arg;
      }
    }

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
