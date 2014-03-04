<?php
class UsersFixture extends Fixture {
	var $type = "users";
	function setUp() {
		$this->records = array(
			"omar" => array(
				"first_name" => "Omar",
				"last_name" => "Admin",
				"username" => "admin",
				"email" => "admin@localhost",
				"password" => "#adm1n",
				"groups" => "admin,-~"
			),
			"abdul" => array(
				"first_name" => "Abdul",
				"last_name" => "User",
				"username" => "user",
				"email" => "abdul@localhost",
				"password" => "#us3r",
				"groups" => "user,-~"
			)
		);
		$this->storeAll();
	}
}
?>
