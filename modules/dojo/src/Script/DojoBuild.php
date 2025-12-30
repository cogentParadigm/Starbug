<?php
namespace Starbug\Dojo\Script;

use Starbug\Dojo\Service\DojoConfiguration;

class DojoBuild {
  public function __construct(DojoConfiguration $dojo, $base_directory) {
    $this->dojo = $dojo;
    $this->base_directory = $base_directory;
  }
  public function __invoke() {
    if (!file_exists($this->base_directory."/var/etc")) {
      passthru("mkdir ".$this->base_directory."/var/etc");
    }
    file_put_contents($this->base_directory."/var/etc/dojo.profile.js", $this->dojo->getBuildProfile());
  }
}
