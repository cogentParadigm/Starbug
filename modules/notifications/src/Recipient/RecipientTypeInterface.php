<?php
namespace Starbug\Notifications\Recipient;

interface RecipientTypeInterface {
  public function getId($notification);
  public function getRecipient($notification);
}
