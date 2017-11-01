<?php
namespace Starbug\Core;
use Interop\Container\ContainerInterface;
use Exception;
/**
* an implementation of HookFactoryInterface
*/
class HookFactory implements HookFactoryInterface {
	private $hooks;
	private $container;
	private $classes = array();
	public function __construct(ContainerInterface $container, ResourceLocatorInterface $locator) {
		$this->container = $container;
		$this->locator = $locator;
	}
	public function get($hook) {
		$parts = explode("/", $hook);
		$className = $this->locator->className($parts[0]." ".$parts[1], "Hook");
		if (false == $className) {
			return [];
		}
		$object = $this->container->make($className);
		return [$object];
	}
}
