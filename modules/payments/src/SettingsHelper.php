<?php
namespace Starbug\Payments;

class SettingsHelper {
  public function __construct(
    protected SettingsInterface $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
