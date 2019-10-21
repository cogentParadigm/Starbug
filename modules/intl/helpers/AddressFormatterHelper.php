<?php
namespace Starbug\Intl;

class AddressFormatterHelper {
  public function __construct(AddressFormatter $target) {
    $this->target = $target;
  }
  public function helper() {
    return $this->target;
  }
}
