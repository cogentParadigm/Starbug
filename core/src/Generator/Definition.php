<?php
namespace Starbug\Core\Generator;
class Definition {
	protected $directories = [];
	protected $templates = [];
	protected $copy = [];
	protected $parameters = [];
	protected $module = "app";
	public function setModule($module) {
		$this->module = $module;
	}
	public function getModule() {
		return $this->module;
	}
	public function addDirectory($dir) {
		$this->directories[] = $dir;
	}
	public function getDirectories() {
		return $this->directories;
	}
	public function addCopy($source, $destination) {
		$this->copy[$source] = $destination;
	}
	public function getCopies() {
		return $this->copy;
	}
	public function addTemplate($source, $destination) {
		$this->templates[$source] = $destination;
	}
	public function getTemplates() {
		return $this->templates;
	}
	public function setParameter($key, $value) {
		$this->parameters[$key] = $value;
	}
	public function getParameter($key) {
		return $this->parameters[$key];
	}
	public function hasParameter($key) {
		return isset($this->parameters[$key]);
	}
	public function getParameters() {
		return $this->parameters;
	}
	public function build($options = []) {
		$this->parameters = $options;
		if (!empty($options["module"])) {
			if ($options["module"] == "app") $this->setModule("app");
			else if ($options["module"] == "core") $this->setModule("core/app");
			else $this->setModule("modules/".$options["module"]);
		} else if (!empty($options["theme"])) {
			$this->setModule("app/themes/".$options["theme"]);
		}
	}
}
?>
