<?php
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
