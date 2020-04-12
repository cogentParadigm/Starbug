<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;

/**
 * An implementation of TaskFactoryInterface.
 */
class TaskFactory implements TaskFactoryInterface {
  protected $container;
  protected $locator;
  protected $tasks = [];
  public function __construct(ContainerInterface $container, ResourceLocatorInterface $locator) {
    $this->container = $container;
    $this->locator = $locator;
  }
  public function get($task) {
    if (!isset($this->tasks[$task])) {
      $class = ucwords($task)."Task";
      $namespace = end($this->locator->getNamespaces($class, "tasks"));
      $this->tasks[$task] = $this->container->build($namespace.$class);
    }
    return $this->tasks[$task];
  }
}
