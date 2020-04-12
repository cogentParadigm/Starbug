<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Exception;

class HelperFactory implements HelperFactoryInterface {
  protected $locator;
  protected $container;
  public function __construct(ResourceLocatorInterface $locator, ContainerInterface $container) {
    $this->container = $container;
    $this->locator = $locator;
  }
  public function get($helper) {
    if ($helper = $this->locator->className($helper, "Helper")) {
      return $this->container->get($helper);
    }
    throw new Exception("Missing helper ".$helper);
  }
}
