<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/app/tests/views/ViewTestCase.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup simpletest
 */
/**
 * @defgroup simpletest
 * simpletest extensions to test apps
 * @ingroup util
 */
/**
 * Used to test views. Extends the WebTestCase from SimpleTest
 * @ingroup simpletest
 */
class ViewTestCase extends WebTestCase {

	/**
	 * login as a test user
	 * @param string $group the group you wish to be logged in as a member of
	 */
	function login($group) {
		$this->get(uri("sb-admin"));
		$this->setField('users[username]', "test_$group");
		$this->setField('users[password]', "test");
		$this->click("Log In");
	}

	/**
	 * logout the test user
	 */
	function logout() {
		$this->get(uri("sb-admin/logout"));
	}

	/**
	 * hook to set up tempory users before testing
	 */
	function setUp() {
		$groups = config("groups");
		foreach($groups as $name => $value) store("users", "username:test_$name  email:$name@localhost  password:test  memberships:$value");
	}

	/**
	 * hook to remove tempory users after testing
	 */
	function tearDown() {
		$groups = config("groups");
		foreach($groups as $name => $value) remove("users", "username='test_$name'");
	}

}
?>
