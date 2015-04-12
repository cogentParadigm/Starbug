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
/**
 * The Fixture class. Fixtures hold data sets used by the testing harness
 * @ingroup Fixture
 */
class UnitTest extends PHPUnit_Framework_TestCase {

	var $fixtures = array();
	var $layers = array();
	
	var $harness;
	
	function __construct() {
		global $harness;
		$this->harness = $harness;
	}
	
	function setUp() {
		foreach ($this->layers as $layer) {
			$this->harness->layer($layer);
		}
		foreach ($this->fixtures as $fixture) {
			$this->harness->fixture($fixture);
		}
	}
	
	function tearDown() {
		foreach ($this->layers as $layer) {
			$this->harness->layer($layer, false);
		}
		foreach ($this->fixtures as $fixture) {
			$this->harness->fixture($fixture, false);
		}
	}
	
}
?>
