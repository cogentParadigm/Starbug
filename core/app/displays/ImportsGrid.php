<?php
namespace Starbug\Core;
class ImportsGrid extends GridDisplay {
	public $model = "imports";
	public $action = "admin";
	function build_display($options) {
		$this->attr("base_url", "admin/imports");
		$this->add(["name", "readonly" => true]);
		if (empty($options['model'])) {
			$this->add(["model", "readonly" => true]);
		}
		$this->add(["created", "readonly" => true]);
		$this->add(["modified", "label" => "Last Modified", "readonly" => true]);
		$this->add(["row_options", "plugin" => "starbug.grid.columns.import_options"]);
	}
}
