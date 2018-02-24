<?php
namespace Starbug\Core;
use \PDO;
/**
* DatabaseInterface cannonical implementation
*/
class Database extends AbstractDatabase {

	public $pdo;

	public function setDatabase($name) {
		$params = $this->config->get("db/".$name);
		$this->pdo = new PDO('mysql:host='.$params['host'].';dbname='.$params['db'], $params['username'], $params['password']);
		$this->database_name = $params['db'];
		$this->prefix = $params['prefix'];
		$this->compiler->setPrefix($this->prefix);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		if (false !== $this->timezone) $this->exec("SET TIME_ZONE='".$this->timezone."'");
	}

	public function exec($statement) {
		return $this->pdo->exec($statement);
	}

	public function prepare($statement) {
		return $this->pdo->prepare($statement);
	}

	public function lastInsertId($name = null) {
		return $this->pdo->lastInsertId($name);
	}

	public function __call($method, $args) {
		if (method_exists($this->pdo, $method)) return call_user_func_array(array($this->pdo, $method), $args);
		throw new Exception('Call to undefined method/class function: ' . $method);
	}
}
