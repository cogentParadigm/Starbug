<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/ConfigInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
/**
 * a simple interface for retrieving configuration data
 */
interface ConfigInterface {

	/**
	 * get a configuration value
	 * @param string $name the name of the configuration entry, such as 'themes' or 'fixtures.base'
	 * @param string $scope the scope/category of the configuration item
	 */
	public function get($key, $scope = "etc");
}
