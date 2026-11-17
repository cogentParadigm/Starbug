<?php
namespace Starbug\Notifications\Recipient;

interface RecipientTypeFactoryInterface {
  public function get($type) : RecipientTypeInterface;
}
