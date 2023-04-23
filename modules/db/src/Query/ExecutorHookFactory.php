<?php
namespace Starbug\Db\Query;

use Psr\Container\ContainerInterface;

class ExecutorHookFactory implements ExecutorHookFactoryInterface {
  protected $container;
  protected $hooks = [];
  public function __construct(ContainerInterface $container, $hooks = []) {
    $this->container = $container;
    $this->hooks = $hooks;
  }
  public function get($hook): ?ExecutorHook {
    if (isset($this->hooks[$hook])) {
      return $this->container->get($this->hooks[$hook]);
    }
    return null;
  }
}
