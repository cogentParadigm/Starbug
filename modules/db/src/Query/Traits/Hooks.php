<?php
namespace Starbug\Db\Query\Traits;

use Starbug\Db\Query\BuilderHookInterface;

trait Hooks {
	protected $hooks = [];

	public function addHook(BuilderHookInterface $hook) {
		$this->hooks[] = $hook;
		return $this;
	}

	protected function invokeHooks($method, $args) {
		foreach ($this->hooks as $hook) {
			call_user_func_array([$hook, $method], $args);
		}
	}
}
