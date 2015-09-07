<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/Config.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
/**
 * An implementation of the ConfigInterface which reads name/value pairs from a database table
 */
class Settings implements SettingsInterface {

	private $db;
	private $settings;

	public function __construct(DatabaseInterface $db) {
	$this->db = $db;
	$this->settings = array();
	}

	/**
	* get a configuration value
	* @param string $name the name of the configuration entry, such as 'site_name'
	* @param string $scope the scope/category of the configuration item
	*/
	public function get($key) {
	 $item = $this->db->query("settings")->condition("name", $key)->one();
	 return $item ? $item['value'] : false;
	}
}
