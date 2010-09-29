<?php
// FILE: core/app/tests/ViewTestCase.php
/**
 *  ViewTestCase class
 * 
 *  @package StarbugPHP
 *  @subpackage core
 *  @author Ali Gangji <ali@neonrain.com>
 * 	@copyright 2008-2010 Ali Gangji
 */
/**
 * Used to test views. Extends the WebTestCase from SimpleTest
 * @package StarbugPHP
 * @subpackage core
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
		$this->click("Log Out");
	}

	function setUp() {
		global $groups;
		foreach($groups as $name => $value) store("users", "username:test_$name  password:test  memberships:$value");
	}
	
	function tearDown() {
		global $groups;
		foreach($groups as $name => $value) remove("users", "username='test_$name'");
	}

}
?>
