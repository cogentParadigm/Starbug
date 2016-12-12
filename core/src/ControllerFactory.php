<?php
namespace Starbug\Core;
use \Interop\Container\ContainerInterface;
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
		$controller = $this->locator->className($controller, "Controller");
		if (false === $controller) {
			$controller = "Starbug\\Core\\Controller";
		}
		$object = $this->container->get($controller);
		if ($object instanceof Controller) {
			return $object;
		} else {
			throw new Exception("ControllerFactoryInterface contract violation. ".$controller." is not an instance of Controller.");
		}
	}
}
