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
$sb->provide("core/lib/test/WebTest");
$sb->import("core/lib/test/Harness");
/**
 * The Fixture class. Fixtures hold data sets used by the testing harness
 * @ingroup Fixture
 */
class WebTest extends PHPUnit_Extensions_Selenium2TestCase {


	var $fixtures = array();
	var $layers = array();
	
	var $harness;

	function setUp() {
		global $harness;
		$this->harness = $harness;
		foreach ($this->layers as $layer) {
			$this->harness->layer($layer);
		}
		foreach ($this->fixtures as $fixture) {
			$this->harness->fixture($fixture);
		}
		$this->setBrowser('chrome');
		$this->setBrowserUrl(Etc::DEFAULT_HOST.Etc::WEBSITE_URL);
	}

	function tearDown() {
		foreach ($this->layers as $layer) {
			$this->harness->layer($layer, false);
		}
		foreach ($this->fixtures as $fixture) {
			$this->harness->fixture($fixture, false);
		}
	}

	public function get($url) {
		$this->url(Etc::DEFAULT_HOST.Etc::WEBSITE_URL.$url);
	}
	
}
?>
