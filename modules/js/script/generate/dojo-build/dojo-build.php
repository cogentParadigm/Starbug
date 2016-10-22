<?php
namespace Starbug\Js;
class DojoBuildGenerateCommand {
	public $dirs = array();
	public $generate = array();
	public $copy = array();
	public function __construct(DojoConfiguration $dojo, $base_directory) {
		$this->dojo = $dojo;
		$this->base_directory = $base_directory;
	}
	public function run($argv) {
		if (!file_exists($this->base_directory."/var/etc")) {
			passthru("mkdir ".$this->base_directory."/var/etc");
		}
		file_put_contents($this->base_directory."/var/etc/dojo.profile.js", $this->dojo->getBuildProfile());
		passthru("cd libraries/util/buildscripts; ./build.sh action=release optimize=shrinksafe layerOptimize=shrinksafe stripConsole=all copyTests=false profile=../../../var/etc/dojo.profile.js cssOptimize=comments");
	}
}
?>
