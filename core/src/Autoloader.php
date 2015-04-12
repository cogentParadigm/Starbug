<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/AutoloaderInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */

class Autoloader implements AutoloaderInterface {
	protected $map = array();
	protected $loaded = array();
	protected $base_directory;
	public function __construct($base_directory) {
		$this->base_directory = $base_directory;
	}
	/**
	 * autoload a class
	 */
	public function autoload($class) {
		if (isset($this->map[$class])) {
			if (!isset($this->loaded[$class])) {
				include($this->base_directory."/".$this->map[$class]);
				$this->loaded[$class] = true;
			}
			return $class;
		}
		return false;
	}
	/**
	 * add mappings
	 */
	public function add($map) {
		$this->map = $map + $this->map;
	}
	/**
	 * Register autoloader
	 */
	public function register() {
		spl_autoload_register(array($this, 'autoload'));
	}
}
