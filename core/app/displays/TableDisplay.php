<?php
class TableDisplay {
	var $type = "table";
	var $template = "table";
	function init($options) {
		$this->attributes["class"][] = "table";
		$this->attributes["class"][] = "table-bordered";
		$this->attributes["class"][] = "table-striped";
		$this->attributes["class"][] = "table-hover";
	}
}
?>
