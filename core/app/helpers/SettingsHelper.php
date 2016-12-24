<?php
namespace Starbug\Core;
class SettingsHelper {
	public function __construct(SettingsInterface $settings) {
		$this->target = $settings;
	}
	public function helper() {
		return $this->target;
	}
}
