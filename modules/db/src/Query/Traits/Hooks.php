<?php
namespace Starbug\Db\Query\Traits;

use Starbug\Db\Query\BuilderHookInterface;

trait Hooks {
  protected $hooks = [];
  protected $extensions = [];

  public function addHook(BuilderHookInterface $hook) {
    $this->hooks[] = $hook;
    return $this;
  }

  protected function invokeHooks($method, $args) {
    foreach ($this->hooks as $hook) {
      call_user_func_array([$hook, $method], $args);
    }
  }

  public function addExtension($method, $extension) {
    $this->extensions[$method] = $extension;
  }

  public function __call($name, $arguments) {
    if (!empty($this->extensions[$name])) {
      $extension = $this->extensions[$name];
      call_user_func_array([$extension, $name], [&$this, $arguments]);
    } elseif (method_exists($this->query, $name)) {
      $result = call_user_func_array([$this->query, $name], $arguments);
      if (!is_null($result)) {
        return $result;
      }
    } else {
      $trace = debug_backtrace();
      trigger_error('Call to undefined method via __call(): '.$name.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_WARNING);
    }
    return $this;
  }

  public function __get($name) {
    if ($name == "model") {
      return $this->query->getTable()->getName();
    }

    //$trace = debug_backtrace();
    //trigger_error('Undefined property via __get(): '.$name.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_NOTICE);
  }
}
