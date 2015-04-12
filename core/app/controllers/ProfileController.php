<?php
class ProfileController {
	function init() {
		$this->assign("model", "users");
	}
	function default_action() {
		$this->assign("id", userinfo("id"));
		$this->assign("form_header", "<h1>Update Profile</h1>");
		$this->assign("form", "users");
		$this->assign("action", "update_profile");
		$this->render("admin/update");
	}
}
?>
