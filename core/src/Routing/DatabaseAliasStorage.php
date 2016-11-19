<?php
namespace Starbug\Core\Routing;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\RequestInterface;
class DatabaseAliasStorage implements AliasStorageInterface {
	public function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	public function addAlias($alias, $path) {
		$this->db->store("aliases", ["path" => $path, "alias" => $alias]);
	}
	public function addAliases($aliases) {
		foreach ($aliases as $alias => $path) {
			$this->addRoute($alias, $path);
		}
	}
	public function getPath(RequestInterface $request) {
		$query = $this->db->query("aliases")->condition("alias", $request->getPath());
		if ($path = $query->one()) {
			return $path["path"];
		}
		return false;
	}
}
?>
