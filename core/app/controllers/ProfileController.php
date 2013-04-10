<?php
class ProfileController {
	function init() {
		assign("model", "users");
	}
	function default_action() {
		assign("id", userinfo("id"));
		assign("form_header", "<h1>Update Profile</h1>");
		assign("form", "users");
		assign("action", "update_profile");
		$this->render("admin/update");
	}
}
?>
