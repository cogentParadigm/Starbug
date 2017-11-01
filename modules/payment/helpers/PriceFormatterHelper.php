<?php
namespace Starbug\Payment;

class PriceFormatterHelper {
  public function __construct(PriceFormatterInterface $target) {
    $this->target = $target;
  }
  public function helper() {
    return $this->target;
  }
}
