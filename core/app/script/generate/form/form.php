<?php
class FormGenerateCommand {
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
			"form" => "app/displays/".ucwords($params['model'])."Form.php"
		);
	}
}
?>
