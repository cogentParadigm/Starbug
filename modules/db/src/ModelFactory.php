<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Exception;
use Starbug\ResourceLocator\ResourceLocatorInterface;

class ModelFactory implements ModelFactoryInterface {
  protected $locator;
  protected $container;
  protected $objects;
  public function __construct(ResourceLocatorInterface $locator, ContainerInterface $container, $base_directory) {
    $this->locator = $locator;
    $this->container = $container;
    $this->base_directory = $base_directory;
    $this->objects = [];
  }
  public function has($collection) {
    return (!empty($collection) && (!empty($this->objects[$collection]) || (file_exists($this->base_directory."/var/models/".str_replace(" ", "", ucwords(str_replace("_", " ", $collection)))."Model.php"))));
  }
  public function get($model): Table {
    if (!isset($this->objects[$model])) {
      $className = $this->locator->className($model);
      if (false == $className) {
        $className = "Starbug\\Core\\".str_replace(" ", "", ucwords(str_replace("_", " ", $model)))."Model";
      }
      $this->objects[$model] = $this->container->get($className);
    }
    return $this->objects[$model];
  }
}
