<?php
namespace Starbug\Intl\Helper;

use Starbug\Intl\AddressFormatter;

class AddressFormatterHelper {
  public function __construct(AddressFormatter $target) {
    $this->target = $target;
  }
  public function helper() {
    return $this->target;
  }
}
