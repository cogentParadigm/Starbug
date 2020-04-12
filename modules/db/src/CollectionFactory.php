<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Exception;

class CollectionFactory implements CollectionFactoryInterface {
  protected $locator;
  protected $container;
  public function __construct(ResourceLocatorInterface $locator, ContainerInterface $container) {
    $this->locator = $locator;
    $this->container = $container;
  }
  public function get($collection) {
    $className = $this->locator->className($collection, "Collection");
    if (false === $className) {
      $className = "Starbug\\Core\\Collection";
    }
    $object = $this->container->get($className);
    if ($object instanceof CollectionInterface) {
      return $object;
    } else {
      throw new Exception("CollectionFactoryInterface contract violation. ".$className." is not an instance of Starbug\Core\Collection.");
    }
  }
}
