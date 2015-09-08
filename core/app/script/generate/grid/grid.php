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
		$this->fields = column_info($params['model']);
		$this->generate = array(
			"grid" => "app/displays/".ucwords($params['model'])."Grid.php"
		);
	}
}
?>
