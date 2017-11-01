<?php
namespace Starbug\Db\Query;

use Interop\Container\ContainerInterface;

class BuilderFactory implements BuilderFactoryInterface {
  protected $extensions = null;
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }
  public function create() {
    if (is_null($this->extensions)) {
      $this->extensions = $container->get("db.query.builder.extensions");
    }
    $builder = $this->container->make("Starbug\Db\Query\BuilderInterface");
    foreach ($this->extensions as $name => $extension) {
      $builder->addExtension($name, $extension);
    }
    return $builder;
  }
}