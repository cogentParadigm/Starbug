<?php
namespace Starbug\Core;

use PHPUnit_Extensions_Selenium2TestCase;
use \Etc;

class WebTest extends PHPUnit_Extensions_Selenium2TestCase {

  public function setUp() {
    $this->setBrowser('chrome');
    $this->setBrowserUrl(Etc::DEFAULT_HOST.Etc::WEBSITE_URL);
  }

  public function get($url) {
    $this->url(Etc::DEFAULT_HOST.Etc::WEBSITE_URL.$url);
  }
}
