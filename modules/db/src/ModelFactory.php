<?php
namespace Starbug\Core;
use \Interop\Container\ContainerInterface;
/**
* an implementation of ModelFactoryInterface
*/
class ModelFactory implements ModelFactoryInterface {
	protected $locator;
	protected $container;
	protected $objects;
	public function __construct(ResourceLocatorInterface $locator, ContainerInterface $container, $base_directory) {
		$this->locator = $locator;
		$this->container = $container;
		$this->base_directory = $base_directory;
		$this->objects = array();
	}
	public function has($collection) {
		return (!empty($collection) && (($this->objects[$collection]) || (file_exists($this->base_directory."/var/models/".ucwords($collection)."Model.php"))));
	}
	public function get($collection) {
		$model = $this->locator->className($model);
		if (false === $model) {
			if ($this->has($collection)) {
				$model = "Starbug\\Core\\".ucwords($collection)."Model";
			} else {
				$model = "Starbug\\Core\\Table";
			}
		}
		$object = $this->container->get($model);
		if ($object instanceof Table) {
			return $object;
		} else {
			throw new Exception("ModelFactoryInterface contract violation. ".$model." is not an instance of Starbug\Core\Table.");
		}
	}
}
