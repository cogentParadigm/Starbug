<?php
namespace Starbug\Core;
use Interop\Container\ContainerInterface;
use Exception;
/**
* an implementation of ControllerFactoryInterface
*/
class ControllerFactory implements ControllerFactoryInterface {
	private $locator;
	private $container;
	public function __construct(ResourceLocatorInterface $locator, ContainerInterface $container) {
		$this->locator = $locator;
		$this->container = $container;
	}
	public function get($controller) {
		$className = $this->locator->className($controller, "Controller");
		if (false === $className) {
			throw new Exception("Controller not found. ".$controller." is not an instance of Controller.");
		}
		$object = $this->container->get($className);
		if ($object instanceof Controller) {
			return $object;
		} else {
			throw new Exception("ControllerFactoryInterface contract violation. ".$controller." is not an instance of Controller.");
		}
	}
}
