<?php
namespace Starbug\Core;
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
