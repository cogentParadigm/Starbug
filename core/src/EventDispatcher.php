<?php
namespace Starbug\Core;
class EventDispatcher {

	private $subscribers = array();

	public function subscribe($event, $callable) {
	$this->subscribers[$event][] = $callable;
	}

	public function publish($event, $data) {
	 if (!empty($this->subscribers[$event])) {
	  foreach ($this->subscribers[$event] as $caller) {
	   if (is_callable($caller)) call_user_func_array($caller, $data);
	  }
	 }
	}
}
