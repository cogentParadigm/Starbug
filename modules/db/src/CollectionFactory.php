<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Exception;
use Starbug\ResourceLocator\ResourceLocatorInterface;

class CollectionFactory implements CollectionFactoryInterface {
  protected $locator;
  protected $container;
  public function __construct(ResourceLocatorInterface $locator, ContainerInterface $container) {
    $this->locator = $locator;
    $this->container = $container;
  }
  public function get($collection) {
    if (false !== strpos($collection, "\\")) {
      $className = $collection;
    } else {
      $className = $this->locator->className($collection, "Collection");
    }
    $object = $this->container->get($className);
    if ($object instanceof CollectionInterface) {
      return $object;
    } else {
      throw new Exception("CollectionFactoryInterface contract violation. ".$className." is not an instance of Starbug\Core\Collection.");
    }
  }
}
