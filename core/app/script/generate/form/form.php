<?php
namespace Starbug\Core;
class FormGenerateCommand {
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
			"form" => "app/displays/".ucwords($params['model'])."Form.php"
		);
	}
}
?>
