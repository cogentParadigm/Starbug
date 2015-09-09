<?php
namespace Starbug\Core;
class UsersGrid extends GridDisplay {
	public $model = "users";
	public $action = "admin";
	function build_display($options) {
		$this->add("first_name", "last_name", "email", "last_visit", "groups", "statuses  label:Status");
	}
}
?>
