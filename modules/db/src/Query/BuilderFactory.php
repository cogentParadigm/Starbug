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
      $this->extensions = $this->container->get("db.query.builder.extensions");
    }
    $builder = $this->container->make("Starbug\Db\Query\BuilderInterface");
    $schemer = $this->container->get('Starbug\Db\Schema\SchemerInterface');
    $builder->setSchema($schemer->getSchema());
    foreach ($this->extensions as $name => $extension) {
      $builder->addExtension($name, $extension);
    }
    return $builder;
  }
}