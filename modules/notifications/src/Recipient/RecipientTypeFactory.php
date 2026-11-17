<?php
namespace Starbug\Notifications\Recipient;

use Psr\Container\ContainerInterface;

class RecipientTypeFactory implements RecipientTypeFactoryInterface {
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }
  public function get($type) : RecipientTypeInterface {
    return $this->container->get($type);
  }
}
