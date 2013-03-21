<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
import("lib/test/ModelTest", "core");
class UsersTest extends ModelTest {
	
	var $model = "users";
		
	function test_create() {
		remove("users", "email='phpunit@neonrain.com'");
		$this->action("create", star("email:phpunit@neonrain.com  collective:2"));
		$user = get("users", sb("insert_id"));
		//lets verify the explicit values were set
		$this->assertEquals($user['email'], "phpunit@neonrain.com");
		$this->assertEquals($user['collective'], "2");
		//lets also verify that the implicit values were set
		$this->assertEquals($user['memberships'], "2");
		$this->assertEquals($user['status'], "4");
	}

	function test_delete() {
		//first assert that the record exists
		$user = get("users", array("email" => "phpunit@neonrain.com"));
		$this->assertEquals(empty($user), false);
		
		//remove it and assert that the record is gone
		$this->action("delete", $user);
		$user = get("users", array("email" => "phpunit@neonrain.com"));
		$this->assertEquals($user['status'], "1");
		remove("users", "email='phpunit@neonrain.com'");
	}

}
?>
