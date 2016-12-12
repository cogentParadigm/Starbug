<?php
namespace Starbug\Db;
use Starbug\Core\ModelFactoryInterface;
class StoreCommand {
	public function __construct(ModelFactoryInterface $models) {
		$this->models = $models;
	}
	public function run($argv) {
		$name = array_shift($argv);
		$params = $this->parse($argv);
		$instance = $this->models->get($name);
		$instance->store($params);
		if (!$instance->errors()) {
			$id = (empty($params['id'])) ? $instance->insert_id : $params['id'];
			$records = $instance->query()->condition($name.".id", $id)->all();
			$result = array();
  		foreach ($records as $record) $result[] = array_values($record);
  		$table = new \cli\Table();
  		$table->setHeaders(array_keys($records[0]));
  		$table->setRows($result);
  		$table->display();
		} else {
			$errors = $instance->errors("", true);
			$result = array();
  		foreach ($records as $record) $result[] = array_values($record);
			foreach($errors as $col => $arr) {
				foreach($arr as $e => $m) {
					$result[] = array($col, $m);
				}
			}
			$table = new \cli\Table();
  		$table->setHeaders(array("field", "message"));
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
?>
