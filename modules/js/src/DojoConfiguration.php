<?php
namespace Starbug\Js;
use Starbug\Core\ConfigInterface;
class DojoConfiguration {
	protected $options = false;
	public function __construct(ConfigInterface $config) {
		$this->config = $config;
	}
	public function getConfiguration() {
		$this->load();
		return $this->options;
	}
	public function get($key) {
		$this->load();
		return $this->options[$key];
	}
	public function getBuildProfile() {
		$this->load();
		return "dependencies = ".json_encode($this->options);
	}
	public function getPackages() {
		$prefixes = $this->get("prefixes");
		foreach ($prefixes as $idx => $prefix) {
			$prefixes[$idx] = "{name:'".$prefix[0]."',location:'".$prefix[1]."'}";
		}
		return '['.implode(",", $prefixes).']';
	}
	public function getDependencies($name = "dojo.js") {
		$layers = $this->get("layers");
		foreach ($layers as $layer) {
			if ($layer["name"] == $name) {
				return '["'.implode('", "', $layer["dependencies"]).'"]';
			}
		}
	}
	protected function load() {
		if (false === $this->options) {
			$this->options = $this->config->get("dojo");
		}
	}
}
?>
