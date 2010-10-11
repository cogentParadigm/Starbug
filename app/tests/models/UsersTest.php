<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
class UsersTest extends UnitTestCase {
	
	function __construct() {
			parent::__construct('Users Model Tester');
	}
		
	function test_create() {
		global $sb;
		store("users", "username:simpletest  password:test  memberships:2");
		$user = query("users", "where:username='simpletest' && password='".md5('test')."'  limit:1");
		$this->assertEqual($sb->record_count, 1);
	}

	function test_delete() {
		global $sb;
		$user = query("users", "where:username='simpletest'  limit:1");
		$this->assertEqual($sb->record_count, 1);
		remove("users", "id='".$user['id']."'");
		$this->assertEqual($sb->record_count, 1);
		$user = query("users", "where:username='simpletest'  limit:1");
		$this->assertEqual($sb->record_count, 0);
	}

}
?>
