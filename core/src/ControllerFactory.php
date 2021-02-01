<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Exception;
use Starbug\ResourceLocator\ResourceLocatorInterface;

/**
 * An implementation of ControllerFactoryInterface.
 */
class ControllerFactory implements ControllerFactoryInterface {
  private $locator;
  private $container;
  public function __construct(ResourceLocatorInterface $locator, ContainerInterface $container) {
    $this->locator = $locator;
    $this->container = $container;
  }
  public function get($controller) {
    $className = $this->locator->className($controller, "Controller");
    if (false == $className) {
      throw new Exception("Controller not found. ".$controller.".");
    }
    return $this->container->get($className);
  }
}
