<?php
class AdminSettingsController {
	function init() {
		assign("model", "settings");
	}
	function default_action() {
		$this->render("settings");
	}
}
?>
