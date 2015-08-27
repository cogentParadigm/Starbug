<?php
class ImportsGrid extends GridDisplay {
	public $model = "imports";
	public $action = "admin";
	function build_display($options) {
		$this->add("name", "model", "created", "modified  label:Last Modified");
		$this->add("row_options  plugin:starbug.grid.columns.import_options");
	}
}
?>
