<?php
class ProvincesGrid extends GridDisplay {
	public $model = "provinces";
	public $action = "admin";
	function build_display($options) {
		$this->add("countries_id");
		$this->add("name");
		$this->add("code");
	}
}
?>
