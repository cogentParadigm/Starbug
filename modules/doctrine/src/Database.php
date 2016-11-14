<?php
namespace Starbug\Doctrine;
use Starbug\Core\AbstractDatabase;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;
use \Etc;
class Database extends AbstractDatabase {
	protected $connection;
	public function setDatabase($name) {
		$db = $this->config->get("db/".$name);
		$params = [
			"url" => $db["type"]."://".urlencode($db["username"]).":".$db["password"].'@'.$db["host"].'/'.$db["db"].'?charset=utf8'
		];
		$this->connection = DriverManager::getConnection($params, new Configuration());
		$this->database_name = $db['db'];
		$this->prefix = $db['prefix'];
		if (false !== $this->timezone) $this->exec("SET time_zone='".$this->timezone."'");
	}

	public function exec($statement) {
		return $this->connection->query($statement);
	}

	public function prepare($statement) {
		return $this->connection->prepare($statement);
	}

	public function lastInsertId($name = null) {
		return $this->connection->lastInsertId($name);
	}

	public function getConnection() {
		return $this->connection;
	}
}
?>
