<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Exception;
use Starbug\ResourceLocator\ResourceLocatorInterface;

/**
 * An implementation of HookFactoryInterface.
 */
class HookFactory implements HookFactoryInterface {
  private $hooks;
  private $container;
  private $classes = [];
  public function __construct(ContainerInterface $container, ResourceLocatorInterface $locator) {
    $this->container = $container;
    $this->locator = $locator;
  }
  public function get($hook) {
    $parts = explode("/", $hook);
    $className = $this->locator->className($parts[0]." ".$parts[1], "Hook");
    if (false == $className) {
      return [];
    }
    $object = $this->container->make($className);
    return [$object];
  }
}
