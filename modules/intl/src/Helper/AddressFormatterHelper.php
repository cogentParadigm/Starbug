<?php
namespace Starbug\Intl\Helper;

use Starbug\Intl\AddressFormatter;

class AddressFormatterHelper {
  public function __construct(
    protected AddressFormatter $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
