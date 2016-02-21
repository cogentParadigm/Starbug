<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file core/src/Bundle.php
* @author Ali Gangji <ali@neonrain.com>
* @ingroup core
*/
namespace Starbug\Core;
/**
* Request class. interprets the request (URI, $_GET, and $_POST) and serves the appropriate content
* @ingroup core
*/
class Bundle implements \IteratorAggregate, \Countable {
	protected $data;

	public function __construct(array $data = array()) {
		$this->data = $data;
	}
	public function get() {
		$args = func_get_args();
		$value = $this->data;
		foreach ($args as $arg) {
			$value = $value[$arg];
		}
		return $value;
	}

	public function has($key) {
		$args = func_get_args();
		$target = $this->data;
		while (!empty($args)) {
			$key = array_shift($args);
			if (is_array($target) && array_key_exists($key, $target)) {
				$target = $target[$key];
			} else {
				return false;
			}
		}
		return true;
	}

	public function set($value) {
		$args = func_get_args();
		$target = &$this->data;
		$value = array_pop($args);
		foreach ($args as $arg) {
			$target = &$target[$arg];
		}
		$target = $value;
		return $this;
	}

	public function getIterator() {
		return new \ArrayIterator($this->data);
	}

	public function count() {
		return count($this->data);
	}
}
