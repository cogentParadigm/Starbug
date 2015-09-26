<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
namespace Starbug\Core;
class UsersTest extends ModelTest {

	var $model = "users";

	function test_create() {
		$this->db->remove("users", "email:phpunit@neonrain.com");
		$this->action("create", star("email:phpunit@neonrain.com  groups:user"));
		$user = $this->db->query("users")->select("users.*,users.groups as groups")
							->condition("users.id", $this->insert_id)->condition("users.statuses.slug", "deleted", "!=", array("ornull" => true))->one();
		//lets verify the explicit values were set
		$this->assertEquals($user['email'], "phpunit@neonrain.com");
		//lets also verify that the implicit values were set
		$this->assertEquals($user['groups'], "user");
	}

	function test_delete() {
		//first assert that the record exists
		$user = $this->db->get("users", array("email" => "phpunit@neonrain.com"), array("limit" => 1));
		$this->assertEquals(empty($user), false);

		//remove it and assert that the record is gone
		$this->action("delete", $user);
		$user = $this->db->query("users")->select("users.*,users.statuses.slug as statuses,users.groups as groups")
							->condition("email", "phpunit@neonrain.com")->one();
		$this->assertEquals($user['statuses'], "deleted");
		$this->db->remove("users_groups", "users_id:".$user['id']);
		$this->db->remove("users", "email:phpunit@neonrain.com");
	}

}
?>
