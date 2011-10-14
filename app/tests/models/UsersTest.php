<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
class UsersTest extends PHPUnit_Framework_TestCase {
		
	function test_create() {
		sb("users", "create", star("username:simpletest  email:ali@neonrain.com  collective:2"));
		$user = get("users", sb("insert_id"));
		$this->assertEquals(sb("record_count"), 1);
	}

	function test_delete() {
		$user = query("users", "where:username='simpletest'  limit:1");
		$this->assertEquals(sb("record_count"), 1);
		remove("users", "id='".$user['id']."'");
		$this->assertEquals(sb("record_count"), 1);
		$user = query("users", "where:username='simpletest'  limit:1");
		$this->assertEquals(sb("record_count"), 0);
	}

}
?>
