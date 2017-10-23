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
		}
		return $this;
	}
}
