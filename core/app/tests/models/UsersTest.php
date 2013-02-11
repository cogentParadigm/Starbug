<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
class UsersTest extends PHPUnit_Framework_TestCase {
		
	function test_create() {
		sb("users", "create", star("username:PHPUnit  email:phpunit@neonrain.com  collective:2"));
		$user = get("users", sb("insert_id"));
		//lets verify the explicit values were set
		$this->assertEquals($user['username'], "PHPUnit");
		$this->assertEquals($user['email'], "phpunit@neonrain.com");
		$this->assertEquals($user['collective'], "2");
		//lets also verify that the implicit values were set
		$this->assertEquals($user['memberships'], "2");
	}

	function test_delete() {
		//first assert that the record exists
		$user = get("users", array("username" => "PHPUnit"));
		$this->assertEquals(empty($user), false);
		
		//remove it and assert that the record is gone
		remove("users", "id='".$user['id']."'");
		$user = get("users", array("username" => "PHPUnit"));
		$this->assertEquals(empty($user), true);
	}

}
?>
