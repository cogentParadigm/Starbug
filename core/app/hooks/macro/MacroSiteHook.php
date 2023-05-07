<?php
namespace Starbug\Core;

use Starbug\Settings\SettingsInterface;

class MacroSiteHook extends MacroHook {
  public function __construct(SettingsInterface $settings) {
    $this->settings = $settings;
  }
  public function replace($macro, $name, $token, $data) {
    if ($name == "name") {
      $name = "site_name";
    }
    return $this->settings->get($name);
  }
}
