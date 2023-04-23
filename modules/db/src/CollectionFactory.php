<?php
namespace Starbug\Db;

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
    $className = $this->locator->className($collection, "Collection");
    return $this->container->get($className);
  }
}
