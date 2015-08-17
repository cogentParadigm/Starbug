<?php
class LoginForm extends FormDisplay {
	public $model = "users";
	public $default_action = "login";
	public $submit_label = "Login";
	function build_display($options) {
		unset($this->request->data['users']['password']);
		$this->add("email", "password");
	}
}
?>
