<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Exception;
use Starbug\Http\ResponseBuilderInterface;
use Starbug\ResourceLocator\ResourceLocatorInterface;

/**
 * An implementation of ControllerFactoryInterface.
 */
class ControllerFactory implements ControllerFactoryInterface {
  private $locator;
  private $container;
  public function __construct(ResourceLocatorInterface $locator, ResponseBuilderInterface $responseBuilder, ContainerInterface $container) {
    $this->locator = $locator;
    $this->responseBuilder = $responseBuilder;
    $this->container = $container;
  }
  public function get($controller) {
    $className = $this->locator->className($controller, "Controller");
    if (false == $className) {
      throw new Exception("Controller not found. ".$controller.".");
    }
    $object = $this->container->get($className);
    if ($object instanceof Controller) {
      $object->setResponseBuilder($this->responseBuilder);
      return $object;
    } else {
      throw new Exception("ControllerFactoryInterface contract violation. ".$controller." is not an instance of Controller.");
    }
  }
}
