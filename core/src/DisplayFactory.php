<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Exception;
use Starbug\ResourceLocator\ResourceLocatorInterface;

/**
 * An implementation of DisplayFactoryInterface.
 */
class DisplayFactory implements DisplayFactoryInterface {
  protected $locator;
  protected $container;
  public function __construct(ContainerInterface $container, ResourceLocatorInterface $locator) {
    $this->container = $container;
    $this->locator = $locator;
  }
  public function get($displays): Display {
    if (!is_array($displays)) {
      $displays = [$displays];
    }
    foreach ($displays as $display) {
      if ($display = $this->locator->className($display)) {
        return $this->container->make($display);
      }
    }
    throw new Exception("Display not found: ".implode(", ", $displays));
  }
}
