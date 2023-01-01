<?php
namespace Starbug\Imports\Transform;

use Psr\Container\ContainerInterface;

class Factory {
  protected $container;
  protected $transformers = [];
  public function __construct(ContainerInterface $container, $transformers) {
    $this->container = $container;
    $this->transformers = $transformers;
  }
  public function getTransformers() {
    return $this->transformers;
  }
  public function get($name): TransformerInterface {
    return $this->container->get($this->transformers[$name]["class"]);
  }
}