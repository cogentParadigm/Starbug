<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
import("lib/test/ModelTest", "core");
class UsersTest extends ModelTest {

	var $model = "users";

	function test_create() {
		remove("users", "email:phpunit@neonrain.com");
		$this->action("create", star("email:phpunit@neonrain.com  groups:user"));
		$user = query("users")->select("users.*,users.statuses as statuses,users.groups as groups")
							->condition("users.id", sb("users")->insert_id)->condition("users.statuses.slug", "deleted", "!=")->one();
		//lets verify the explicit values were set
		$this->assertEquals($user['email'], "phpunit@neonrain.com");
		//lets also verify that the implicit values were set
		$this->assertEquals($user['groups'], "user");
	}

	function test_delete() {
		//first assert that the record exists
		$user = get("users", array("email" => "phpunit@neonrain.com"), array("limit" => 1));
		$this->assertEquals(empty($user), false);

		//remove it and assert that the record is gone
		$this->action("delete", $user);
		$user = query("users")->select("users.*,users.statuses as statuses,users.groups as groups")
							->condition(array(
								"email" => "phpunit@neonrain.com",
								"users.statuses" => "deleted"
							))->one();
		$this->assertEquals($user['statuses'], "deleted");
		remove("users_groups", "users_id:".$user['id']);
		remove("users", "email:phpunit@neonrain.com");
	}

}
?>
