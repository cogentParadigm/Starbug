<?php
namespace Starbug\Core\Generator\Definitions;

use Starbug\Core\Generator\Definition;

class Host extends Definition {
  public function build(array $options = []) {
    parent::build($options);
    if (!file_exists("var/etc/di.php")) {
      $this->addDirectory("var/etc");
      $this->setParameter("hmac_key", md5(uniqid(rand(), true)));
      $this->addTemplate("generate/host/di.php", "var/etc/di.php");
    }
  }
}
