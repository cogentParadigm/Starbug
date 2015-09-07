<?php
namespace Starbug\Core;
class ConfigHelper {
	public function __construct(ConfigInterface $config) {
		$this->config = $config;
	}
	public function helper() {
		return $this->config;
	}
}
?>
