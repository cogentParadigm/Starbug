<?php
class LoginForm extends FormDisplay {
	public $model = "users";
	public $default_action = "login";
	public $submit_label = "Login";
	function build_display($options) {
		$this->add("email", "password");
	}
}
?>
