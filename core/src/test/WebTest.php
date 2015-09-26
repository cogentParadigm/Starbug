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
namespace Starbug\Core;
use \Etc;
/**
 * The Fixture class. Fixtures hold data sets used by the testing harness
 * @ingroup Fixture
 */
class WebTest extends \PHPUnit_Extensions_Selenium2TestCase {

	function setUp() {
		$this->setBrowser('chrome');
		$this->setBrowserUrl(Etc::DEFAULT_HOST.Etc::WEBSITE_URL);
	}

	public function get($url) {
		$this->url(Etc::DEFAULT_HOST.Etc::WEBSITE_URL.$url);
	}

}
?>
