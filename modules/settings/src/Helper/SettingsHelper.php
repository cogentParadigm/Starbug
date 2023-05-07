<?php
namespace Starbug\Settings\Helper;

use Starbug\Settings\SettingsInterface;

class SettingsHelper {
  public function __construct(SettingsInterface $settings) {
    $this->target = $settings;
  }
  public function helper() {
    return $this->target;
  }
}
