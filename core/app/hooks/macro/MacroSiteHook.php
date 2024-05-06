<?php
namespace Starbug\Core;

use Starbug\Settings\SettingsInterface;

class MacroSiteHook extends MacroHook {
  public function __construct(
    protected SettingsInterface $settings
  ) {
  }
  public function replace($macro, $name, $token, $data) {
    if ($name == "name") {
      $name = "site_name";
    }
    return $this->settings->get($name);
  }
}
