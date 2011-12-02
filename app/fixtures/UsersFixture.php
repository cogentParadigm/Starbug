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
				"password" => "#adm1n"
			),
			"abdul" => array(
				"first_name" => "Abdul",
				"last_name" => "User",
				"username" => "user",
				"email" => "abdul@localhost",
				"password" => "#us3r",
			)
		);
		$this->records["omar"]["memberships"] = config("groups.admin");
		$this->records["omar"]["collective"] = config("groups.admin");
		$this->records["abdul"]["memberships"] = config("groups.user");
		$this->records["abdul"]["collective"] = config("groups.user");
		$this->storeAll();
	}
}
?>
