<?php
namespace Starbug\Core;

use DI\FactoryInterface;
use ReflectionClass;
use Starbug\ResourceLocator\ResourceLocatorInterface;

class ModelFactory implements ModelFactoryInterface {
  protected $locator;
  protected $factory;
  protected $objects;
  public function __construct(ResourceLocatorInterface $locator, FactoryInterface $factory) {
    $this->locator = $locator;
    $this->factory = $factory;
    $this->objects = [];
  }
  public function get($model): Table {
    if (!isset($this->objects[$model])) {
      $className = $this->locator->className($model);
      if (false == $className) {
        $className = "Starbug\\Core\\Table";
      }
      $this->objects[$model] = $this->factory->make($className);
      $reflectionClass = new ReflectionClass($this->objects[$model]);
      $reflectionProperty = $reflectionClass->getProperty("type");
      $reflectionProperty->setAccessible(true);
      $reflectionProperty->setValue($this->objects[$model], $model);
    }
    return $this->objects[$model];
  }
}
