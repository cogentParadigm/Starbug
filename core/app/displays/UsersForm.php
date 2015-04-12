<?php
class UsersForm extends FormDisplay {
	public $model = "users";
	public $cancel_url = "admin/users";
	function build_display($options) {
		$this->layout->add("top  left:div.col-md-6  right:div.col-md-6");
		$this->layout->put('left', 'h2', 'User Information');
		$this->layout->put('right', 'h2', 'Login Credentials');
		$this->add("first_name  pane:left");
		$this->add("last_name  pane:left");
		$this->add("email  pane:right");
		$this->add("password  pane:right");
		$this->add("password_confirm  input_type:password  pane:right");
		$this->add("groups  input_type:multiple_category_select  taxonomy:groups  pane:right");
	}
}
?>
