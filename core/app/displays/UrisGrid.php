<?php
class UrisGrid extends GridDisplay {
	public $model = "uris";
	public $action = "admin";
	function build_display($options) {
		$this->add("title", "statuses", "modified  label:Last Modified");
	}
}
?>
