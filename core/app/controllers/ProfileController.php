<?php
class ProfileController {
	function init() {
		$this->assign("model", "users");
	}
	function default_action() {
		$this->assign("id", userinfo("id"));
		$this->render("profile");
	}
}
?>
