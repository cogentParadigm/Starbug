<?php
namespace Starbug\Core;

use Psr\Container\ContainerInterface;
use Starbug\Core\FormHook;

class FormHookFactory implements FormHookFactoryInterface {
  protected $container;
  protected $hooks = [];
  public function __construct(ContainerInterface $container, $hooks = []) {
    $this->container = $container;
    $this->hooks = $hooks;
  }
  public function get($hook): ?FormHook {
    if (isset($this->hooks[$hook])) {
      return $this->container->get($this->hooks[$hook]);
    }
    return null;
  }
}
