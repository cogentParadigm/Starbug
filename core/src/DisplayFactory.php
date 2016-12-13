<?php
namespace Starbug\Core;
use \Interop\Container\ContainerInterface;
/**
* an implementation of DisplayFactoryInterface
*/
class DisplayFactory implements DisplayFactoryInterface {
	protected $locator;
	protected $container;
	public function __construct(ContainerInterface $container, ResourceLocatorInterface $locator) {
		$this->container = $container;
		$this->locator = $locator;
	}
	public function get($displays) {
		if (!is_array($displays)) $displays = [$displays];
		foreach ($displays as $display) {
			if ($display = $this->locator->className($display)) {
				$object = $this->container->make($display);
				if ($object instanceof Display) {
					return $object;
				} else {
					throw new Exception("DisplayFactoryInterface contract violation. ".$display." is not an instance of Starbug\\Core\\Display.");
				}
			}
		}
		throw new Exception("Display not found: ".implode(", ", $displays));
	}
}
