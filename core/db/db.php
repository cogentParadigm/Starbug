<?php
// FILE: core/db/db.php
/**
 * The db class
 * 
 * @package StarbugPHP
 * @subpackage core
 * @author Ali Gangji <ali@neonrain.com>
 * @copyright 2008-2010 Ali Gangji
 */
/**
 * The db class. A simple PDO wrapper
 * @package StarbugPHP
 * @subpackage core
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
