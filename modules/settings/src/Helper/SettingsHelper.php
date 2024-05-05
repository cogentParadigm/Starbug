<?php
namespace Starbug\Settings\Helper;

use Starbug\Settings\SettingsInterface;

class SettingsHelper {
  public function __construct(
    protected SettingsInterface $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
