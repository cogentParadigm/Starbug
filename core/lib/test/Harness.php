<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file core/lib/Harness.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup Harness
 */
/**
 * @defgroup test
 * @ingroup lib
 */
/**
 * @defgroup Harness
 * Testing Harness
 * @ingroup test
 */
$sb->provide("core/lib/test/Harness");
$sb->import("core/lib/test/Fixture");
/**
 * The Harness class. Handles fixtures and test execution
 * @ingroup Harness
 */
class Harness {
	/**
	 * @var array fixture layers from etc/fixtures.json
	 */
	var $layers = array();
	/**
	 * @var array loaded fixtures
	 */
	var $fixtures = array();

	/**
	 * constructor. loads layers
	 */
	function __construct() {
		$this->layers = config("fixtures");
	}

	function  clean() {
		foreach ($this->fixtures as $fixture) $fixture->tearDown();
		$this->fixtures = array();
	}

	function layer($layer, $up=true) {
		$dependencies = $this->layers[$layer];
		foreach ($dependencies as $dep) {
			if (isset($this->layers[$dep])) $this->layer($dep, $up);
			else if (file_exists(BASE_DIR."/app/fixtures/".ucwords($dep)."Fixture.php")) $this->fixture($dep, $up);
		}
	}
	
	function fixture($fixture, $up=true) {
		$fixture = ucwords($fixture)."Fixture";
		include(BASE_DIR."/app/fixtures/".$fixture.".php");
		$fixture = new $fixture();
		if ($up) $fixture->setUp();
		else $fixture->tearDown();
	}

}
/**
 * testing harness
 * @ingroup global
 */
global $harness;
$harness = new Harness();
?>
