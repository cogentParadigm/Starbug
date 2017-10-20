<?php
namespace Starbug\Payment;

interface CartHookInterface {
  public function addProduct(&$line, $input);
}