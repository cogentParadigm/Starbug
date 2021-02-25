<?php
namespace Starbug\Payment;

class PaymentSettingsHelper {
  public function __construct(PaymentSettingsInterface $settings) {
    $this->target = $settings;
  }
  public function helper() {
    return $this->target;
  }
}
