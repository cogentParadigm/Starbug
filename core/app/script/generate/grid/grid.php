<?php
namespace Starbug\Core;
class GridGenerateCommand {
	public $dirs = array();
	public $generate = array();
	public $copy = array();
	public $fields = array();
	public function __construct(ModelFactoryInterface $models) {
		$this->models = $models;
	}
	public function run($params) {
		$this->fields = $this->models->get($params['model'])->column_info();
		$this->generate = array(
			"grid" => "app/displays/".ucwords($params['model'])."Grid.php"
		);
	}
}
?>
