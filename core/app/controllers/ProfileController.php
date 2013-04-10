<?php
class ProfileController {
	function init() {
		assign("model", "users");
	}
	function default_action() {
		assign("id", userinfo("id"));
		assign("action", "update_profile");
		$this->render("admin/update");
	}
}
?>
