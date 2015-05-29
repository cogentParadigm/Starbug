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
		$this->layers = json_decode(file_get_contents("etc/fixtures.json"), true);
	}

	function  clean() {
		foreach ($this->fixtures as $fixture) $fixture->_tearDown();
		$this->fixtures = array();
	}

	function layer($layer, $up=true) {
		$dependencies = $this->layers[$layer];
		if (!$up) $dependencies = array_reverse($dependencies);
		foreach ($dependencies as $dep) {
			if (isset($this->layers[$dep])) $this->layer($dep, $up);
			else if (file_exists(BASE_DIR."/app/fixtures/".ucwords($dep)."Fixture.php")) $this->fixture($dep, $up);
		}
	}

	function fixture($fixture, $up=true) {
		if (empty($this->fixtures[$fixture])) {
			$classname = ucwords($fixture)."Fixture";
			include(BASE_DIR."/app/fixtures/".$classname.".php");
			$this->fixtures[$fixture] = new $classname();
		}
		if ($up) $this->fixtures[$fixture]->_setUp();
		else $this->fixtures[$fixture]->_tearDown();
	}

}
/**
 * testing harness
 * @ingroup global
 */
global $harness;
$harness = new Harness();
?>
