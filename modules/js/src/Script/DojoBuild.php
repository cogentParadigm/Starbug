<?php
namespace Starbug\Js\Script;

use Starbug\Js\DojoConfiguration;

class DojoBuild {
  public function __construct(
    protected DojoConfiguration $dojo,
    protected $base_directory
  ) {
  }
  public function __invoke() {
    if (!file_exists($this->base_directory."/var/etc")) {
      passthru("mkdir ".$this->base_directory."/var/etc");
    }
    file_put_contents($this->base_directory."/var/etc/dojo.profile.js", $this->dojo->getBuildProfile());
    passthru("cd libraries/util/buildscripts; ./build.sh action=release optimize=shrinksafe layerOptimize=shrinksafe stripConsole=all copyTests=false profile=../../../var/etc/dojo.profile.js cssOptimize=comments");
  }
}
