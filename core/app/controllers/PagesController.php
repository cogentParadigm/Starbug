<?php
class PagesController {
	function show() {
		$this->render("blocks", array("region" => "content"), array("scope" => "templates"));
	}
}
?>
