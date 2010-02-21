<?php
/**
* FILE: core/db/db.php
* PURPOSE: db class. A simple PDO wrapper.
* 
* This file is part of StarbugPHP
*
* StarbugPHP - website development kit
* Copyright (C) 2008-2009 Ali Gangji
*
* StarbugPHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* StarbugPHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.
*/
class db {

	var $pdo;
	public $debug = false;

	public function __construct($dsn, $username=false, $password=false) {
		try {
			$this->pdo = new PDO($dsn, $username, $password);
			$this->set_debug(false);
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
