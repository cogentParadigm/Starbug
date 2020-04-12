<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Exception;

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
  public function get($model) {
    $className = $this->locator->className($model);
    if (false === $className) {
      $className = "Starbug\\Core\\".str_replace(" ", "", ucwords(str_replace("_", " ", $model)))."Model";
    }
    $object = $this->container->get($className);
    if ($object instanceof Table) {
      return $object;
    } else {
      throw new Exception("ModelFactoryInterface contract violation. ".$model." is not an instance of Starbug\Core\Table.");
    }
  }
}
