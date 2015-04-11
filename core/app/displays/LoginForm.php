<?php
class LoginForm extends FormDisplay {
	public $model = "users";
	public $default_action = "login";
	function build_display($options) {
		$this->add("email", "password");
	}
}
?>
