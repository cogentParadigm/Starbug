<?php
namespace Starbug\Db\Query;

use Psr\Container\ContainerInterface;
use Starbug\Core\QueryHook;

class ExecutorHookFactory implements ExecutorHookFactoryInterface {
  protected $container;
  protected $hooks = [];
  public function __construct(ContainerInterface $container, $hooks = []) {
    $this->container = $container;
    $this->hooks = $hooks;
  }
  public function get($hook): ?QueryHook {
    if (isset($this->hooks[$hook])) {
      return $this->container->get($this->hooks[$hook]);
    }
    return null;
  }
}
