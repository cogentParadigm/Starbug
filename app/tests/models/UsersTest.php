<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
class UsersTest extends PHPUnit_Framework_TestCase {
		
	function test_create() {
		sb("users", "create", star("username:PHPUnit  email:ali@neonrain.com  collective:2"));
		$user = get("users", sb("insert_id"));
		//lets verify the explicit values were set
		$this->assertEquals($user['username'], "PHPUnit");
		$this->assertEquals($user['email'], "ali@neonrain.com");
		$this->assertEquals($user['collective'], "2");
		//lets also verify that the implicit values were set
		$this->assertEquals($user['memberships'], "2");
	}

	function test_delete() {
		$user = get("users", array("username" => "PHPUnit"));
		$this->assertEquals(sb("record_count"), 1);
		remove("users", "id='".$user['id']."'");
		$this->assertEquals(sb("record_count"), 1);
		$user = get("users", array("username" => "PHPUnit"));
		$this->assertEquals($user, false);
	}

}
?>
