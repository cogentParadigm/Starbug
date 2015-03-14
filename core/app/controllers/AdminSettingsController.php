<?php
class AdminSettingsController {
	function init() {
		$this->assign("model", "settings");
	}
	function default_action() {
		$this->render("settings");
	}
}
?>
