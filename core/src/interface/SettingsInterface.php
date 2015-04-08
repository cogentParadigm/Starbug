<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/ConfigInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
/**
 * a simple interface for retrieving settings
 */
interface SettingsInterface {

	/**
	 * get a settings value
	 * @param string $name the name of the settings entry, such as 'site_name' or 'theme'
	 */
	public function get($key);
}
