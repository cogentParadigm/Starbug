<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/ModelTest.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup ModelTest
 */
/**
 * @defgroup ModelTest
 * the base test class for models
 * @ingroup test
 */
$sb->provide("core/lib/test/ModelTest");
/**
 * The Fixture class. Fixtures hold data sets used by the testing harness
 * @ingroup Fixture
 */
class ModelTest extends PHPUnit_Framework_TestCase {
	var $model;
	/**
	 * overridable function
	 * set $records here to prevent storing all records
	 */
	function setUp() {}
	/**
	 * overridable function
	 * unset $records to prevent removing records
	 */
	function tearDown() {}
	/**
	 * setUp function
	 */
	function _setUp() {
		$this->setUp();
	}
	/**
	 * tearDown function
	 */
	function _tearDown() {
		$this->tearDown();
	}
	
	function action() {
		$args = func_get_args();
		$method = array_shift($args);
		return call_user_func_array(array(sb($this->model), $method), $args);
	}
	
}
?>
