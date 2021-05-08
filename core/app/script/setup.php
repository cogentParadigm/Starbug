<?php
namespace Starbug\Core;

use Starbug\Core\Generator\Definitions\Host;
use Starbug\Core\Generator\Generator;
use Starbug\Db\Schema\SchemerInterface;

class SetupCommand {
  public function __construct(Generator $generator, Host $host, SchemerInterface $schemer) {
    $this->generator = $generator;
    $this->host = $host;
    $this->schemer = $schemer;
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

    // Generate host configuration
    $this->generator->generate($this->host, $named);

    // Migrate database
    $this->schemer->migrate();
  }
}
