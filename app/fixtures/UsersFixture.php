<?php
use Starbug\Core\Fixture;
class UsersFixture extends Fixture {
	public $type = "users";
	public $records = array(
			"omar" => array(
				"first_name" => "Omar",
				"last_name" => "Admin",
				"email" => "admin@localhost",
				"password" => "#adm1n",
				"groups" => "admin,-~"
			),
			"abdul" => array(
				"first_name" => "Abdul",
				"last_name" => "User",
				"email" => "abdul@localhost",
				"password" => "#us3r",
				"groups" => "user,-~"
			)
	);
}
?>
