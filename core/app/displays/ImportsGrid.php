<?php
namespace Starbug\Core;
class ImportsGrid extends GridDisplay {
	public $model = "imports";
	public $action = "admin";
	function build_display($options) {
		$this->add("name  readonly:", "model  readonly:", "created  readonly:", "modified  label:Last Modified  readonly:");
		$this->add("row_options  plugin:starbug.grid.columns.import_options");
	}
}
?>
