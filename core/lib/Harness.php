<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * @file util/Harness.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
$sb->provide("util/Harness");
include("core/db/Fixture.php");
/**
 * The Harness class. Handles fixtures and test execution
 * @ingroup util
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
		$this->layers = json_decode(file_get_contents(BASE_DIR."/etc/fixtures.json"), true);
		$this->layer("base");
	}

	function  clean() {
		foreach ($this->fixtures as $fixture) $fixture->tearDown();
		$this->fixtures = array();
	}

	function layer($layer) {
		$dependencies = $this->layers[$layer];
		foreach ($dependencies as $dep) {
			if (isset($this->layers[$dep])) $this->layer($dep);
			else if (file_exists(BASE_DIR."/app/fixtures/".ucwords($dep)."Fixture.php")) $this->fixture($dep);
		}
	}
	
	function fixture($fixture) {
		$fixture = ucwords($fixture)."Fixture";
		include(BASE_DIR."/app/fixtures/".$fixture.".php");
		$fixture = new $fixture();
		$fixture->setUp();
	}

}
?>
