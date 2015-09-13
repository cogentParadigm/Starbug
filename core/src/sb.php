<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/sb.php
 * The global sb object. provides data, errors, import/provide, load and pub/sub. The backbone of Starbug
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
namespace Starbug\Core;
use \Etc;
/**
 * The sb class. Provides data, errors, import/provide, pub/sub, and load to the rest of the application. The core component of StarbugPHP.
 * @ingroup core
 */
class sb {
	public $db;
	static $instance;
	public $models;

	/**
	* constructor. connects to db and starts the session
	*/
	function __construct(DatabaseInterface $db, ModelFactoryInterface $models) {
		$this->db = $db;
		$this->models = $models;
		if (defined("Etc::DEBUG")) $this->db->set_debug(Etc::DEBUG);
		self::$instance = $this;
	}

	/**
	* get a model by name
	* @param string $name the name of the model, such as 'users'
	* @return the instantiated model
	*/
	function get($name) {
		return $this->models->get($name);
	}
}
