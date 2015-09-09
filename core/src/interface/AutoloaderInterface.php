<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/AutoloaderInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
interface AutoloaderInterface {
	/**
	 * autoload a class
	 */
	public function autoload($class);
	/**
	 * add mappings
	 */
	public function add($map);
	/**
	 * Register autoloader
	 */
	public function register();
}
