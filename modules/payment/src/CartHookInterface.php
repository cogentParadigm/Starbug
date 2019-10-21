<?php
namespace Starbug\Payment;

interface CartHookInterface {
  public function addProduct($product, &$line, &$input);
}
