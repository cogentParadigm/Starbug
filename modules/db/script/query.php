<?php
namespace Starbug\Db;
use Starbug\Core\DatabaseInterface;
class QueryCommand {
	public function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	public function run($argv) {
		$name = array_shift($argv);
		$params = $this->parse($argv);
		$records = $this->db->query($name, $params);
		echo $records->interpolate()."\n";
		if (!empty($params['limit']) && $params['limit'] == 1) $records = array($records);
		else $records = $records->execute();
		if (empty($records)) {
			echo "..no results\n";
		} else {
			$result = array();
			foreach ($records as $record) $result[] = array_values($record);
			$table = new \cli\Table();
			$table->setHeaders(array_keys($records[0]));
			$table->setRows($result);
			$table->display();
		}
	}
	public function parse($args) {
		$params = array();
		foreach ($args as $arg) {
			$arg = explode(":", $arg);
			$params[$arg[0]] = $arg[1];
		}
		return $params;
	}
}
