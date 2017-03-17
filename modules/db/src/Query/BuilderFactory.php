<?php
namespace Starbug\Db\Query;

use Interop\Container\ContainerInterface;

class BuilderFactory implements BuilderFactoryInterface {
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}
	public function create() {
		return $this->container->make("Starbug\Db\Query\BuilderInterface");
	}
}