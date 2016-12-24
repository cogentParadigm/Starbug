<?php
namespace Starbug\Core;
class MacroSiteHook extends MacroHook {
	function __construct(SettingsInterface $settings) {
		$this->settings = $settings;
	}
	function replace($macro, $name, $token, $data) {
		if ($name == "name") $name = "site_name";
		return $this->settings->get($name);
	}
}
?>
