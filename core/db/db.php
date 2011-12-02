<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/db/db.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
/**
 * @defgroup db
 * the db class
 * @ingroup db
 */
/**
 * The db class. A simple PDO wrapper
 * @ingroup db
 */
class db {

	/**
	 * @var PDO a PDO object
	 */
	var $pdo;
	/**
	 * @var bool true if in debug mode
	 */
	public $debug = false;

	public function __construct($dsn, $username=false, $password=false) {
		try {
			$this->pdo = new PDO($dsn, $username, $password);
			$this->set_debug(false);
			if (defined('Etc::TIME_ZONE')) $this->exec("SET time_zone='".Etc::TIME_ZONE."'");
		} catch (PDOException $e) { 
			die("PDO CONNECTION ERROR: " . $e->getMessage() . "\n");
		}
	}

	public function set_debug($debug) {
		$this->debug = (bool) $debug;
		if ($this->debug == true) $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		else $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
	}

  public function exec($statement) {
		try {
			return $this->pdo->exec($statement);
		} catch(PDOException $e) { 
			die("DB Exception: ".$e->getMessage()."\n");
		}
	}

	public function query($statement) { 
		try {
			return $this->pdo->query($statement);
		} catch(PDOException $e) { 
			die("DB Exception: ".$e->getMessage()."\n"); 
		}
	}

	public function __call($method, $args) {
		if(method_exists($this->pdo, $method)) return call_user_func_array(array($this->pdo, $method), $args);
		throw new Exception ('Call to undefined method/class function: ' . $method);
	}

}
?>
