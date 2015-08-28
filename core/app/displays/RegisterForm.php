<?php
class RegisterForm extends FormDisplay {
	public $model = "users";
	public $default_action = "register";
	public $submit_label = "Register";
	function build_display($options) {
		unset($this->request->data['users']['password']);
		unset($this->request->data['users']['password_confirm']);
		$this->add("email", "password", "password_confirm  input_type:password");
	}
}
?>
