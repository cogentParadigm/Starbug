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
  public function get($collection): CollectionInterface {
    if (false !== strpos($collection, "\\")) {
      $className = $collection;
    } else {
      $className = $this->locator->className($collection, "Collection");
    }
    return $this->container->get($className);
  }
}
