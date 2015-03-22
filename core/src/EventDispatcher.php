<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/EventDispatcher.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
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
?>
